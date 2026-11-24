(function () {
    const fieldSelector = 'input, select, textarea';
    let nextErrorId = 0;

    function normalizeName(name) {
        return (name || '').replace(/\[([^\]]+)\]/g, '.$1');
    }

    function isField(field) {
        return field.name &&
            !field.disabled &&
            !field.readOnly &&
            !['hidden', 'button', 'submit', 'reset', 'image'].includes(field.type);
    }

    function isVisible(field) {
        if (field.closest('[hidden], dialog:not([open])')) return false;
        for (let node = field; node; node = node.parentElement) {
            if (getComputedStyle(node).display === 'none') return false;
        }
        return true;
    }

    function fieldsFor(form, key) {
        return Array.from(form.querySelectorAll(fieldSelector))
            .filter(field => isField(field) && normalizeName(field.name) === key);
    }

    function positiveGroupKey(form) {
        return form.dataset.requireOnePositive || 'items';
    }

    function prepareFieldIndicator(field) {
        if (['checkbox', 'radio', 'hidden', 'button', 'submit', 'reset', 'image'].includes(field.type) || field.readOnly) return;
        let control = field.parentElement;
        if (!control.classList.contains('validation-control')) {
            const hasToggle = Boolean(control.querySelector('.pw-eye'));
            const wrapper = document.createElement('span');
            wrapper.className = 'validation-control';
            if (hasToggle) wrapper.dataset.hasToggle = 'true';
            field.before(wrapper);
            wrapper.appendChild(field);
            control = wrapper;
        }

        if (!control.querySelector('.validation-indicator')) {
            const indicator = document.createElement('span');
            indicator.className = 'validation-indicator';
            indicator.textContent = '✓';
            indicator.setAttribute('aria-hidden', 'true');
            control.appendChild(indicator);
        }
    }

    function showFieldIndicator(field, valid) {
        if (['checkbox', 'radio', 'hidden', 'button', 'submit', 'reset', 'image'].includes(field.type)) return;
        const control = field.parentElement;
        if (!control || !control.classList.contains('validation-control')) return;
        const shouldShow = valid &&
            field.dataset.validationTouched === 'true' &&
            field.value.trim() !== '';
        control.dataset.valid = shouldShow ? 'true' : 'false';
    }

    function errorNode(form, key, field, container) {
        let node = Array.from(form.querySelectorAll('[data-validation-error-for]'))
            .find(item => item.dataset.validationErrorFor === key);

        if (!node) {
            node = document.createElement('span');
            node.className = 'err';
            node.dataset.validationErrorFor = key;
            node.style.display = 'none';

            const label = field && field.labels && field.labels[0];
            if (container) {
                container.appendChild(node);
            } else if (field && field.type === 'radio' && field.closest('[data-validation-group]')) {
                field.closest('[data-validation-group]').appendChild(node);
            } else if (label && label.classList.contains('ck')) {
                label.insertAdjacentElement('afterend', node);
            } else if (field && field.closest('.rp-field')) {
                field.closest('.rp-field').insertAdjacentElement('afterend', node);
            } else if (label) {
                label.appendChild(node);
            } else if (field) {
                field.insertAdjacentElement('afterend', node);
            }
        }

        if (!node.id) node.id = 'validasi-field-' + (++nextErrorId);
        node.setAttribute('role', 'alert');
        node.setAttribute('aria-live', 'polite');
        return node;
    }

    function linkError(fields, node) {
        fields.filter(Boolean).forEach(field => {
            const ids = new Set((field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
            ids.add(node.id);
            field.setAttribute('aria-describedby', Array.from(ids).join(' '));
        });
    }

    function setError(form, field, key, message, container) {
        const node = errorNode(form, key, field, container);
        node.textContent = message;
        node.style.display = 'block';
        const group = key === positiveGroupKey(form) && form.hasAttribute('data-require-one-positive')
            ? Array.from(form.querySelectorAll('[data-optional-positive]'))
            : fieldsFor(form, key);
        group.forEach(item => item.setAttribute('aria-invalid', 'true'));
        group.forEach(item => showFieldIndicator(item, false));
        linkError(group.length ? group : [field], node);
    }

    function clearError(form, key) {
        const node = Array.from(form.querySelectorAll('[data-validation-error-for]'))
            .find(item => item.dataset.validationErrorFor === key);
        const group = key === positiveGroupKey(form) && form.hasAttribute('data-require-one-positive')
            ? Array.from(form.querySelectorAll('[data-optional-positive]'))
            : fieldsFor(form, key);
        group.forEach(item => item.removeAttribute('aria-invalid'));
        group.forEach(item => {
            if (item.dataset.validationTouched === 'true') showFieldIndicator(item, true);
        });
        if (node) {
            group.forEach(item => {
                const ids = (item.getAttribute('aria-describedby') || '')
                    .split(/\s+/)
                    .filter(id => id && id !== node.id);
                if (ids.length) item.setAttribute('aria-describedby', ids.join(' '));
                else item.removeAttribute('aria-describedby');
            });
            node.textContent = '';
            node.style.display = 'none';
        }
        return true;
    }

    function fieldLabel(field) {
        if (field.dataset.validationLabel) return field.dataset.validationLabel;
        const label = field.labels && field.labels[0];
        if (!label) return 'bagian ini';

        const copy = label.cloneNode(true);
        copy.querySelectorAll('.err, button, small').forEach(node => node.remove());
        return copy.textContent.replace(/\s+/g, ' ').trim() || 'bagian ini';
    }

    function requiredMessage(field) {
        const messages = {
            email: 'Masukkan alamat email agar dapat melanjutkan.',
            name: 'Masukkan nama lengkap agar dapat melanjutkan.',
            telepon: 'Masukkan nomor telepon agar dapat melanjutkan.',
            kategori: 'Pilih apakah Ibu warga Taman Gading atau pendaftar umum.',
            blok: 'Masukkan blok rumah, misalnya A.',
            nomor_rumah: 'Masukkan nomor rumah.',
            alamat: 'Masukkan alamat lengkap.',
            password: 'Masukkan kata sandi.',
            password_confirmation: 'Ketik ulang kata sandi.',
            persetujuan_privasi: 'Centang persetujuan Kebijakan Privasi untuk melanjutkan.',
            nominal: 'Masukkan jumlah penarikan.',
            metode: 'Pilih tunai atau transfer.',
            nama_bank: 'Masukkan nama bank atau e-wallet tujuan.',
            nomor_rekening: 'Masukkan nomor rekening atau nomor HP e-wallet.',
            nama_pemilik: 'Masukkan nama pemilik rekening atau akun.',
            nasabah_id: 'Pilih nasabah.',
            alasan: 'Tuliskan catatan agar pendaftar mengetahui tindak lanjutnya.',
            alasan_pembatalan: 'Tuliskan alasan pembatalan agar dapat ditinjau.',
            alasan_penolakan: 'Tuliskan alasan agar nasabah mengetahui tindak lanjutnya.',
            konfirmasi: 'Ketik teks konfirmasi sesuai petunjuk.',
            harga_per_kg: 'Masukkan harga per satuan.',
            berlaku_mulai: 'Pilih tanggal mulai berlakunya harga.',
        };

        if (field.type === 'checkbox') return 'Centang kotak ini agar dapat melanjutkan.';
        if (field.type === 'radio') return messages[field.name] || 'Pilih salah satu opsi.';
        return messages[field.name] || 'Isi ' + fieldLabel(field) + ' agar dapat melanjutkan.';
    }

    function patternMessage(field) {
        if (field.dataset.validationPatternMessage) return field.dataset.validationPatternMessage;

        const messages = {
            telepon: 'Gunakan angka dan tanda + saja. Hapus spasi atau tanda hubung.',
            blok: 'Gunakan huruf, angka, atau tanda minus untuk mengisi blok.',
            nomor_rumah: 'Gunakan huruf, angka, garis miring, atau tanda minus.',
            password: 'Tambahkan minimal satu huruf dan satu angka.',
            konfirmasi: 'Ketik ANONIMKAN dengan huruf kapital.',
        };

        return messages[field.name] || 'Periksa format isian ini dan ikuti petunjuk di kolom.';
    }

    function valueMessage(field) {
        const value = field.value;
        const validity = field.validity;
        const name = field.name;

        if (field.required && !value.trim() && field.type !== 'radio' && field.type !== 'checkbox') {
            return requiredMessage(field);
        }
        if (validity.valueMissing) return requiredMessage(field);

        if (field.dataset.match) {
            const match = document.getElementById(field.dataset.match);
            if (match && value !== match.value) {
                return match.value
                    ? 'Kata sandi belum sama. Periksa kembali kedua isian.'
                    : 'Isi kata sandi di atas terlebih dahulu.';
            }
        }

        if (field.dataset.exactValue && value !== field.dataset.exactValue) {
            return patternMessage(field);
        }

        if (validity.typeMismatch) {
            return field.type === 'email'
                ? 'Periksa format email, misalnya nama@email.com.'
                : 'Periksa format isian ini.';
        }
        if (validity.patternMismatch) return patternMessage(field);
        if (validity.tooShort) {
            return 'Gunakan minimal ' + field.minLength + ' karakter agar isian ini dapat digunakan.';
        }
        if (validity.tooLong) {
            return 'Gunakan maksimal ' + field.maxLength + ' karakter untuk isian ini.';
        }
        if (validity.rangeUnderflow) {
            return field.type === 'date'
                ? 'Pilih tanggal yang sama dengan atau setelah ' + field.min + '.'
                : 'Masukkan angka yang lebih besar.';
        }
        if (validity.rangeOverflow) {
            return field.type === 'date'
                ? 'Pilih tanggal yang sama dengan atau sebelum ' + field.max + '.'
                : 'Masukkan angka yang lebih kecil.';
        }
        if (validity.stepMismatch) return 'Gunakan kelipatan ' + field.step + ' untuk isian ini.';

        if (field.classList.contains('rp-input') && value) {
            const amount = Number(value.replace(/\D/g, ''));
            const wrapper = field.closest('[data-rupiah]');
            const minimum = Number(field.dataset.minValue || 0);
            if (amount < minimum) return 'Masukkan jumlah lebih dari 0.';
            if (wrapper && wrapper.dataset.max && amount > Number(wrapper.dataset.max)) {
                return 'Jumlah melebihi saldo tersedia. Masukkan jumlah yang sama atau lebih kecil.';
            }
        }

        if (field.hasAttribute('data-optional-positive') && value.trim()) {
            const amount = Number(value.trim().replace(',', '.'));
            if (!Number.isFinite(amount)) return 'Masukkan berat dengan angka dan maksimal dua angka di belakang koma.';
            if (amount <= 0) return 'Masukkan berat lebih dari 0, atau kosongkan jika tidak menyetor jenis ini.';
        }

        if (validity.badInput) return 'Periksa kembali angka yang dimasukkan.';
        return '';
    }

    function radioRepresentative(form, field) {
        if (field.type !== 'radio') return field;
        return Array.from(form.querySelectorAll('input[type="radio"]'))
            .find(item => item.name === field.name) || field;
    }

    function validateField(form, field) {
        if (!isField(field) || !isVisible(field)) return true;
        field = radioRepresentative(form, field);
        const key = normalizeName(field.name);
        const fields = fieldsFor(form, key);
        const existing = Array.from(form.querySelectorAll('[data-validation-error-for]'))
            .find(item => item.dataset.validationErrorFor === key);

        if (field.dataset.validationServerError === 'true' && field.dataset.validationEdited !== 'true') {
            showFieldIndicator(field, false);
            return false;
        }

        const message = valueMessage(field);
        if (message) {
            setError(form, field, key, message);
            showFieldIndicator(field, false);
            return false;
        }

        if (existing || fields.some(item => item.hasAttribute('aria-invalid'))) {
            clearError(form, key);
        }
        showFieldIndicator(field, true);
        return true;
    }

    function positiveGroupValid(form) {
        const fields = Array.from(form.querySelectorAll('[data-optional-positive]')).filter(isField);
        return fields.some(field => Number(field.value.trim().replace(',', '.')) > 0);
    }

    function validatePositiveGroup(form) {
        if (!form.hasAttribute('data-require-one-positive')) {
            return { valid: true };
        }

        const key = positiveGroupKey(form);
        const fields = Array.from(form.querySelectorAll('[data-optional-positive]')).filter(isField);
        if (positiveGroupValid(form)) {
            clearError(form, key);
            fields.forEach(field => validateField(form, field));
            return { valid: true };
        }

        const first = fields[0];
        const container = Array.from(form.querySelectorAll('[data-validation-group]'))
            .find(item => item.dataset.validationGroup === key);
        setError(form, first, key, 'Masukkan berat lebih dari 0 untuk setidaknya satu jenis sampah.', container);
        form.dataset.validationGroupTouched = 'true';
        return { valid: false, field: first };
    }

    function setupForm(form) {
        if (form.dataset.validationReady === 'true' || form.method === 'dialog') return;
        form.dataset.validationReady = 'true';
        form.noValidate = true;

        form.querySelectorAll(fieldSelector).forEach(prepareFieldIndicator);
        form.querySelectorAll('[data-validation-error-for]').forEach(node => {
            if (!node.textContent.trim()) return;
            const key = node.dataset.validationErrorFor;
            if (key === positiveGroupKey(form) && form.hasAttribute('data-require-one-positive')) {
                const fields = Array.from(form.querySelectorAll('[data-optional-positive]')).filter(isField);
                form.dataset.validationGroupTouched = 'true';
                fields.forEach(field => {
                    field.dataset.validationTouched = 'true';
                    field.setAttribute('aria-invalid', 'true');
                });
                linkError(fields, node);
                return;
            }
            fieldsFor(form, key).forEach(field => {
                field.dataset.validationTouched = 'true';
                field.dataset.validationServerError = 'true';
                field.setAttribute('aria-invalid', 'true');
                linkError([field], node);
            });
        });
    }

    document.querySelectorAll('form').forEach(setupForm);

    document.addEventListener('focusout', event => {
        const field = event.target;
        if (!field.matches || !field.matches(fieldSelector) || !field.form) return;
        const form = field.form;
        setupForm(form);
        const representative = radioRepresentative(form, field);
        const members = fieldsFor(form, normalizeName(representative.name));
        members.forEach(item => { item.dataset.validationTouched = 'true'; });
        validateField(form, representative);
        if (form.dataset.validationGroupTouched === 'true') validatePositiveGroup(form);
    });

    function onEdit(event) {
        const field = event.target;
        if (!field.matches || !field.matches(fieldSelector) || !field.form) return;
        const form = field.form;
        setupForm(form);
        field.dataset.validationEdited = 'true';
        field.dataset.validationServerError = 'false';

        const representative = radioRepresentative(form, field);
        const members = fieldsFor(form, normalizeName(representative.name));
        if (members.some(item => item.dataset.validationTouched === 'true') || form.dataset.validationSubmitted === 'true') {
            members.forEach(item => {
                item.dataset.validationEdited = 'true';
                item.dataset.validationServerError = 'false';
            });
            validateField(form, representative);
        }

        if (form.dataset.validationGroupTouched === 'true') validatePositiveGroup(form);

        if (field.id) {
            form.querySelectorAll('[data-match]').forEach(dependent => {
                if (dependent.dataset.match === field.id && dependent.dataset.validationTouched === 'true') {
                    validateField(form, dependent);
                }
            });
        }
    }

    document.addEventListener('input', onEdit);
    document.addEventListener('change', onEdit);

    document.addEventListener('submit', event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || (event.submitter && event.submitter.formNoValidate)) return;
        setupForm(form);
        form.dataset.validationSubmitted = 'true';

        let firstInvalid = null;
        let valid = true;
        Array.from(form.querySelectorAll(fieldSelector)).forEach(field => {
            if (!isField(field) || !isVisible(field)) return;
            const representative = radioRepresentative(form, field);
            const fields = fieldsFor(form, normalizeName(representative.name));
            fields.forEach(item => { item.dataset.validationTouched = 'true'; });
            if (!validateField(form, representative)) {
                valid = false;
                firstInvalid = firstInvalid || representative;
            }
        });

        const group = validatePositiveGroup(form);
        if (!group.valid) {
            valid = false;
            firstInvalid = firstInvalid || group.field;
        }

        if (!valid) {
            event.preventDefault();
            if (firstInvalid) firstInvalid.focus();
        }
    }, true);
})();
