/**
 * Design Cart Spin To Win — admin UI helpers
 *
 * @author  Paweł Nosko
 * @company Design Cart
 */
(function () {
  'use strict';

  function syncCouponCard(card) {
    var input = card.querySelector('.dc-stw-coupon__input');
    if (!input) return;
    card.classList.toggle('is-selected', !!input.checked);
  }

  function initCouponTiles(root) {
    root = root || document;
    root.querySelectorAll('.dc-stw-coupon').forEach(function (card) {
      var input = card.querySelector('.dc-stw-coupon__input');
      if (!input || input._dcStwBound) return;
      input._dcStwBound = true;

      syncCouponCard(card);
      input.addEventListener('change', function () {
        syncCouponCard(card);
      });
    });
  }

  function boot() {
    initCouponTiles(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
