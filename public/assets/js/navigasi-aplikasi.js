(function () {
    'use strict';

    var content = document.getElementById('isi');
    if (!content) return;

    var isNavigating = false;

    function isAppUrl(url) {
        return url.origin === window.location.origin && /^\/(admin|nasabah)(\/|$)/.test(url.pathname);
    }

    function showError(message) {
        var toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('on');
        window.setTimeout(function () { toast.classList.remove('on'); }, 5000);
    }

    function updateShell(page, url) {
        document.title = page.title;
        document.body.dataset.view = page.body.dataset.view || 'home';
        document.body.dataset.role = page.body.dataset.role || document.body.dataset.role;

        var title = page.querySelector('#top .crumb b');
        var currentTitle = document.querySelector('#top .crumb b');
        if (title && currentTitle) currentTitle.textContent = title.textContent;

        var balance = page.querySelector('#top .right .bal');
        var currentBalance = document.querySelector('#top .right .bal');
        if (balance && currentBalance) currentBalance.innerHTML = balance.innerHTML;

        var nextLinks = Array.from(page.querySelectorAll('#keys a[href]'));
        document.querySelectorAll('#keys a[href]').forEach(function (link) {
            var next = nextLinks.find(function (item) { return item.href === link.href; });
            link.classList.toggle('on', !!(next && next.classList.contains('on')));
        });

        var toast = page.querySelector('#toast');
        var currentToast = document.getElementById('toast');
        if (toast && currentToast) {
            currentToast.replaceWith(toast.cloneNode(true));
            var updatedToast = document.getElementById('toast');
            if (updatedToast.classList.contains('on')) {
                window.setTimeout(function () { updatedToast.classList.remove('on'); }, 5000);
            }
        }

        if (window.toggleSide) window.toggleSide(false);
        window.history.replaceState(
            Object.assign({}, window.history.state, { url: url.href }),
            '',
            window.location.href
        );
    }

    function executeScripts(scripts) {
        return scripts.reduce(function (chain, original) {
            return chain.then(function () {
                var type = original.type || '';
                if (type && type !== 'text/javascript' && type !== 'application/javascript' && type !== 'module') {
                    return;
                }

                var script = document.createElement('script');
                Array.from(original.attributes).forEach(function (attribute) {
                    script.setAttribute(attribute.name, attribute.value);
                });
                script.textContent = original.textContent;

                if (!script.src) {
                    document.body.appendChild(script);
                    script.remove();
                    return;
                }

                script.async = false;
                return new Promise(function (resolve, reject) {
                    script.addEventListener('load', function () {
                        script.remove();
                        resolve();
                    }, { once: true });
                    script.addEventListener('error', function () {
                        script.remove();
                        reject(new Error('Gagal memuat skrip halaman: ' + script.src));
                    }, { once: true });
                    document.body.appendChild(script);
                });
            });
        }, Promise.resolve());
    }

    async function renderPage(response, target, options) {
        var contentType = response.headers.get('content-type') || '';
        if (!contentType.toLowerCase().includes('text/html')) {
            if (options.method === 'GET') {
                window.location.assign(response.url || target.href);
                return false;
            }
            showError('Perubahan belum dapat ditampilkan. Muat ulang halaman untuk memastikan statusnya.');
            return false;
        }

        var html = await response.text();
        var page = new DOMParser().parseFromString(html, 'text/html');
        var nextContent = page.getElementById('isi');
        var dataNode = page.getElementById('garasi-data');
        if (!nextContent || !dataNode) {
            if (response.redirected) {
                window.location.assign(response.url);
            } else if (options.method === 'GET') {
                window.location.assign(response.url || target.href);
            } else {
                showError('Halaman tidak dapat diperbarui. Periksa kembali status perubahan sebelum mencoba lagi.');
            }
            return false;
        }

        var garasiData;
        try {
            garasiData = JSON.parse(dataNode.textContent);
        } catch (error) {
            console.error('Data ruangan 3D tidak dapat dibaca.', error);
            showError('Halaman gagal diperbarui. Silakan muat ulang.');
            return false;
        }

        var executable = function (script) {
            var type = script.type || '';
            return !type || type === 'text/javascript' || type === 'application/javascript' || type === 'module';
        };
        var scripts = Array.from(nextContent.querySelectorAll('script')).filter(executable);
        var pageScripts = page.getElementById('page-scripts');
        if (pageScripts) scripts = scripts.concat(Array.from(pageScripts.querySelectorAll('script')).filter(executable));
        scripts.forEach(function (script) { script.remove(); });

        var currentPageScripts = document.getElementById('page-scripts');
        updateShell(page, new URL(response.url || target.href));
        content.innerHTML = nextContent.innerHTML;
        if (currentPageScripts) currentPageScripts.replaceChildren();

        window.GARASI = garasiData;
        if (window.refreshGarasiData) window.refreshGarasiData(garasiData);

        if (options.push) {
            var destination = response.url || target.href;
            if (destination === window.location.href) {
                window.history.replaceState({ scrollY: 0 }, '', destination);
            } else {
                window.history.pushState(
                    Object.assign({}, window.history.state, { scrollY: 0 }),
                    '',
                    destination
                );
            }
        } else if (options.replace || options.popstate) {
            window.history.replaceState(
                Object.assign({}, window.history.state, { scrollY: 0 }),
                '',
                response.url || target.href
            );
        }

        try {
            await executeScripts(scripts);
        } catch (error) {
            console.error(error);
            showError('Sebagian fitur halaman gagal dimuat. Silakan muat ulang.');
            return false;
        }

        document.dispatchEvent(new CustomEvent('app:page-ready'));
        window.scrollTo(0, options.scrollY || 0);
        return true;
    }

    async function navigate(url, options) {
        if (isNavigating) return false;
        isNavigating = true;
        content.setAttribute('aria-busy', 'true');

        try {
            var request = {
                method: options.method || 'GET',
                credentials: 'same-origin',
                headers: { Accept: 'text/html' }
            };
            if (options.body) request.body = options.body;

            var response = await fetch(url.href, request);
            if (response.status >= 500 && options.method !== 'GET') {
                showError('Server belum dapat menyelesaikan perubahan. Periksa statusnya sebelum mencoba lagi.');
                return false;
            }
            return await renderPage(response, url, options);
        } catch (error) {
            console.error('Navigasi halaman gagal.', error);
            if (options.method === 'GET') {
                window.location.assign(url.href);
            } else {
                showError('Halaman belum dapat diperbarui. Periksa koneksi, lalu coba lagi.');
            }
            return false;
        } finally {
            content.removeAttribute('aria-busy');
            isNavigating = false;
        }
    }

    function visitLink(url) {
        window.history.replaceState(
            Object.assign({}, window.history.state, { scrollY: window.scrollY }),
            '',
            window.location.href
        );
        navigate(url, { method: 'GET', push: true });
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        var link = event.target.closest('a[href]');
        if (!link || link.target || link.hasAttribute('download') || link.dataset.nativeNavigation !== undefined) return;

        var url = new URL(link.href, window.location.href);
        if (!isAppUrl(url) || url.href === window.location.href) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        if (/\/export-(setoran|penarikan)$/.test(url.pathname)) return;

        event.preventDefault();
        visitLink(url);
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement) || form.id === 'form-logout') return;
        if (form.target || form.dataset.nativeNavigation !== undefined || form.querySelector('input[type="file"]')) return;

        var method = form.method.toUpperCase();
        if (method !== 'GET' && method !== 'POST') return;
        var url = new URL(form.action, window.location.href);
        var formData = new FormData(form);
        if (event.submitter && event.submitter.name) {
            formData.append(event.submitter.name, event.submitter.value);
        }

        if (method === 'GET') {
            url.search = new URLSearchParams(formData).toString();
        }
        if (!isAppUrl(url) && !(method === 'POST' && url.pathname === '/ganti-kata-sandi')) return;

        event.preventDefault();
        var options = { method: method === 'GET' ? 'GET' : 'POST' };
        if (options.method === 'POST') options.body = formData;
        visitForm(url, options);
    });

    function visitForm(url, options) {
        window.history.replaceState(
            Object.assign({}, window.history.state, { scrollY: window.scrollY }),
            '',
            window.location.href
        );
        navigate(url, Object.assign(options, { push: true }));
    }

    window.navigateApp = function (href) {
        var url = new URL(href, window.location.href);
        if (isAppUrl(url)) visitLink(url);
        else window.location.assign(url.href);
    };

    window.addEventListener('popstate', function (event) {
        if (isNavigating) {
            window.location.reload();
            return;
        }
        navigate(new URL(window.location.href), {
            method: 'GET',
            popstate: true,
            scrollY: event.state && event.state.scrollY || 0
        });
    });
})();
