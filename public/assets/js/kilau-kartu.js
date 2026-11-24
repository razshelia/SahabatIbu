(function () {
    'use strict';

    const cardSelector = '.card.g, .box.g, .kpi > .g, .grid > .g, .how > div';
    const menuSelector = '#keys a, #keys button, .fold';
    let activeCard = null;

    function updatePointerPosition(element, event) {
        const bounds = element.getBoundingClientRect();
        element.style.setProperty('--mx', (event.clientX - bounds.left) + 'px');
        element.style.setProperty('--my', (event.clientY - bounds.top) + 'px');
    }

    function clearActiveCard() {
        if (!activeCard) return;
        activeCard.classList.remove('pointer-glow');
        activeCard = null;
    }

    document.addEventListener('pointermove', function (event) {
        if (event.pointerType === 'touch') return;

        const card = event.target.closest(cardSelector);
        if (card !== activeCard) {
            clearActiveCard();
            activeCard = card;
            if (activeCard) activeCard.classList.add('pointer-glow');
        }
        if (card) updatePointerPosition(card, event);

        const menuItem = event.target.closest(menuSelector);
        if (menuItem) updatePointerPosition(menuItem, event);
    }, { passive: true });

    document.addEventListener('pointerout', function (event) {
        const nextTarget = event.relatedTarget;
        if (activeCard && (!(nextTarget instanceof Node) || !activeCard.contains(nextTarget))) clearActiveCard();
    });

    window.addEventListener('blur', clearActiveCard);
})();
