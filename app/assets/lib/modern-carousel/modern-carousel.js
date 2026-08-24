(function () {
  'use strict';

  var instances = new WeakMap();

  function reducedMotion() {
    return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  }

  var MQ_MOBILE = '(max-width: 767px)';
  var MQ_TABLET = '(min-width: 768px) and (max-width: 1024px)';

  var DEFAULTS = {
    vd: 3, vt: 3, vm: 1,
    ov: 34, sp: 500, lp: 1,
    ap: 0, de: 4000, ph: 1,
    sa: 1, si: 0.78, oi: 0.75, bi: 0,
    za: 40, zi: 28
  };

  function ModernCarousel(root) {
    this.root = root;
    this.cfg = this.readConfig();
    this.slides = Array.prototype.slice.call(root.querySelectorAll('.modern-carousel__slide'));
    this.dots = Array.prototype.slice.call(root.querySelectorAll('.modern-carousel__dot'));
    this.count = this.slides.length;
    this.index = 0;
    this.locked = false;
    this.lockTimer = null;
    this.timer = null;
    this.hovering = false;
    this.focusedIn = false;
    this.slideW = 0;
    this.destroyed = false;

    this.mqMobile = window.matchMedia ? window.matchMedia(MQ_MOBILE) : null;
    this.mqTablet = window.matchMedia ? window.matchMedia(MQ_TABLET) : null;

    if (this.count < 1) {
      return;
    }

    root.style.setProperty('--mc-speed', (this.cfg.sp / 1000) + 's');

    this.boundKeydown = this.onKeydown.bind(this);
    this.boundPrev = this.prev.bind(this, 'user');
    this.boundNext = this.next.bind(this, 'user');
    this.boundDotClick = this.onDotClick.bind(this);
    this.boundEnter = this.onEnter.bind(this);
    this.boundLeave = this.onLeave.bind(this);
    this.boundFocus = this.onFocusIn.bind(this);
    this.boundBlur = this.onFocusOut.bind(this);

    root.addEventListener('keydown', this.boundKeydown);

    var prevBtn = root.querySelector('[data-mc-prev]');
    var nextBtn = root.querySelector('[data-mc-next]');
    if (prevBtn) { prevBtn.addEventListener('click', this.boundPrev); }
    if (nextBtn) { nextBtn.addEventListener('click', this.boundNext); }

    this.dots.forEach(function (dot) {
      dot.addEventListener('click', this.boundDotClick);
    }, this);

    if (this.cfg.ph) {
      root.addEventListener('mouseenter', this.boundEnter);
      root.addEventListener('mouseleave', this.boundLeave);
    }
    root.addEventListener('focusin', this.boundFocus);
    root.addEventListener('focusout', this.boundBlur);

    if (this.mqMobile) {
      this.mqMobile.addEventListener ? this.mqMobile.addEventListener('change', this.relayoutBound || (this.relayoutBound = this.relayout.bind(this)))
        : this.mqMobile.addListener(this.relayoutBound || (this.relayoutBound = this.relayout.bind(this)));
    }
    if (this.mqTablet) {
      this.mqTablet.addEventListener ? this.mqTablet.addEventListener('change', this.relayoutTabletBound || (this.relayoutTabletBound = this.relayout.bind(this)))
        : this.mqTablet.addListener(this.relayoutTabletBound || (this.relayoutTabletBound = this.relayout.bind(this)));
    }

    if ('ResizeObserver' in window) {
      var self = this;
      this.ro = new ResizeObserver(function () {
        self.relayout();
      });
      this.ro.observe(root.querySelector('.modern-carousel__viewport') || root);
    } else {
      this.boundResize = this.relayout.bind(this);
      window.addEventListener('resize', this.boundResize);
    }

    this.relayout(true);
    this.sync();
    this.startTimer();
  }

  ModernCarousel.prototype.readConfig = function () {
    var cfg = {};
    for (var k in DEFAULTS) {
      cfg[k] = DEFAULTS[k];
    }
    try {
      var raw = JSON.parse(this.root.getAttribute('data-mc') || '{}');
      for (var key in raw) {
        if (typeof DEFAULTS[key] !== 'undefined') {
          cfg[key] = parseFloat(raw[key]);
          if (isNaN(cfg[key])) {
            cfg[key] = DEFAULTS[key];
          }
        }
      }
    } catch (e) {}
    return cfg;
  };

  ModernCarousel.prototype.visibleCount = function () {
    if (this.mqMobile && this.mqMobile.matches) {
      return Math.max(1, Math.round(this.cfg.vm));
    }
    if (this.mqTablet && this.mqTablet.matches) {
      return Math.max(1, Math.round(this.cfg.vt));
    }
    return Math.max(1, Math.round(this.cfg.vd));
  };

  ModernCarousel.prototype.maxDepth = function () {
    return Math.floor(this.visibleCount() / 2);
  };

  ModernCarousel.prototype.measure = function () {
    var w = 0;
    for (var i = 0; i < this.slides.length; i++) {
      if (!this.slides[i].classList.contains('is-offstage')) {
        w = this.slides[i].offsetWidth;
        break;
      }
    }
    if (!w) {
      w = this.slides[0] ? this.slides[0].offsetWidth : 0;
    }
    if (!w) {
      var vp = this.root.querySelector('.modern-carousel__viewport');
      w = vp ? vp.clientWidth * 0.4 : 320;
    }
    this.slideW = w;
  };

  ModernCarousel.prototype.pitch = function () {
    var p = this.slideW - this.cfg.ov;
    var minP = this.slideW * 0.45;
    return Math.max(minP, p);
  };

  ModernCarousel.prototype.deltaOf = function (i) {
    var n = this.count;
    var d = (i - this.index) % n;
    if (d < 0) {
      d += n;
    }
    if (d > n / 2) {
      d -= n;
    }
    return d;
  };

  ModernCarousel.prototype.scaleFor = function (depth, md) {
    if (depth === 0) {
      return this.cfg.sa;
    }
    var extra = (depth - 1) * Math.max(0.06, (this.cfg.sa - this.cfg.si) * 0.35);
    return Math.max(0.4, this.cfg.si - extra);
  };

  ModernCarousel.prototype.opacityFor = function (depth, md) {
    if (depth === 0) {
      return 1;
    }
    if (depth > md) {
      return this.cfg.oi * 0.35;
    }
    return this.cfg.oi;
  };

  ModernCarousel.prototype.blurFor = function (depth) {
    if (depth === 0 || !this.cfg.bi) {
      return 'none';
    }
    return 'blur(' + this.cfg.bi + 'px)';
  };

  ModernCarousel.prototype.zFor = function (depth) {
    var z = [this.cfg.za, this.cfg.zi, Math.max(1, this.cfg.zi - 10), Math.max(1, this.cfg.zi - 20)];
    return z[Math.min(depth, 3)];
  };

  ModernCarousel.prototype.place = function (el, depth, pitch, md) {
    el.style.setProperty('--mc-x', (depth * pitch) + 'px');
    el.style.setProperty('--mc-s', String(this.scaleFor(Math.abs(depth), md)));
  };

  ModernCarousel.prototype.apply = function () {
    var md = this.maxDepth();
    var p = this.pitch();

    for (var i = 0; i < this.count; i++) {
      var el = this.slides[i];
      var d = this.deltaOf(i);
      var ad = Math.abs(d);
      var wasOff = el.classList.contains('is-offstage');
      var prevD = typeof el._mcD === 'number' ? el._mcD : d;

      if (ad > md + 1) {
        el.classList.add('is-offstage');
        this.place(el, d < 0 ? -1 : 1, p, md);
        el._mcD = d;
        continue;
      }

      var entering = wasOff || ad === md + 1 && Math.abs(prevD) > md + 1;

      if (entering && !this.instantLayout) {
        el.classList.add('is-offstage');
        this.place(el, d < 0 ? -1 : 1, p, md);
        void el.offsetWidth;
        el.classList.remove('is-offstage');
      }

      el.style.zIndex = String(this.zFor(ad));
      el.style.opacity = String(this.opacityFor(ad, md));
      el.style.filter = this.blurFor(ad);
      el.style.setProperty('--mc-x', (d * p) + 'px');
      el.style.setProperty('--mc-s', String(this.scaleFor(ad, md)));

      el.classList.toggle('is-active', ad === 0);
      el.classList.toggle('is-prev', d === -1);
      el.classList.toggle('is-next', d === 1);
      el.classList.toggle('is-far', ad > 1);

      var hidden = ad > md;
      el.setAttribute('aria-hidden', hidden ? 'true' : 'false');
      if (hidden) {
        el.setAttribute('inert', '');
      } else {
        el.removeAttribute('inert');
      }

      el._mcD = d;
    }
  };

  ModernCarousel.prototype.sync = function () {
    for (var i = 0; i < this.dots.length; i++) {
      var active = i === this.index;
      this.dots[i].classList.toggle('is-active', active);
      if (active) {
        this.dots[i].setAttribute('aria-selected', 'true');
        this.dots[i].setAttribute('aria-current', 'true');
      } else {
        this.dots[i].setAttribute('aria-selected', 'false');
        this.dots[i].removeAttribute('aria-current');
      }
    }
  };

  ModernCarousel.prototype.relayout = function (instant) {
    if (this.destroyed) {
      return;
    }
    this.measure();
    if (instant) {
      this.instantLayout = true;
      this.root.classList.add('no-anim');
    }
    this.apply();
    if (instant) {
      void this.root.offsetWidth;
      this.root.classList.remove('no-anim');
      this.instantLayout = false;
    }
    if (!instant) {
      this.sync();
    }
  };

  ModernCarousel.prototype.goTo = function (target, source) {
    if (this.destroyed || this.count < 2) {
      return;
    }
    var n = this.count;
    if (this.cfg.lp) {
      target = ((target % n) + n) % n;
    } else {
      target = Math.max(0, Math.min(n - 1, target));
    }
    if (target === this.index) {
      return;
    }
    var wasLocked = this.locked;
    this.locked = true;
    clearTimeout(this.lockTimer);
    if (wasLocked) {
      this.root.classList.add('no-anim');
    }
    this.index = target;
    this.apply();
    this.sync();
    if (wasLocked) {
      void this.root.offsetWidth;
      this.root.classList.remove('no-anim');
    }
    var self = this;
    this.lockTimer = setTimeout(function () {
      self.locked = false;
    }, wasLocked ? 40 : this.cfg.sp + 60);
    if (source === 'user') {
      this.startTimer();
    }
  };

  ModernCarousel.prototype.step = function (dir, source) {
    if (this.locked) {
      return;
    }
    this.goTo(this.index + dir, source);
  };

  ModernCarousel.prototype.next = function (source) {
    this.step(1, source);
  };

  ModernCarousel.prototype.prev = function (source) {
    this.step(-1, source);
  };

  ModernCarousel.prototype.onDotClick = function (e) {
    var target = parseInt(e.currentTarget.getAttribute('data-mc-go'), 10);
    if (!isNaN(target)) {
      this.goTo(target, 'user');
    }
  };

  ModernCarousel.prototype.onKeydown = function (e) {
    switch (e.key) {
      case 'ArrowLeft':
        e.preventDefault();
        this.prev('user');
        break;
      case 'ArrowRight':
        e.preventDefault();
        this.next('user');
        break;
      case 'Home':
        e.preventDefault();
        this.goTo(0, 'user');
        break;
      case 'End':
        e.preventDefault();
        this.goTo(this.count - 1, 'user');
        break;
    }
  };

  ModernCarousel.prototype.onEnter = function () {
    this.hovering = true;
  };

  ModernCarousel.prototype.onLeave = function () {
    this.hovering = false;
  };

  ModernCarousel.prototype.onFocusIn = function () {
    this.focusedIn = true;
  };

  ModernCarousel.prototype.onFocusOut = function () {
    this.focusedIn = false;
  };

  ModernCarousel.prototype.startTimer = function () {
    var self = this;
    this.stopTimer();
    if (!this.cfg.ap || reducedMotion() || this.count < 2 || this.destroyed) {
      return;
    }
    this.timer = setInterval(function () {
      if (document.hidden || self.hovering || self.focusedIn || self.destroyed) {
        return;
      }
      self.step(1, 'auto');
    }, Math.max(1000, this.cfg.de));
  };

  ModernCarousel.prototype.stopTimer = function () {
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
  };

  ModernCarousel.prototype.destroy = function () {
    if (this.destroyed) {
      return;
    }
    this.destroyed = true;
    this.stopTimer();
    clearTimeout(this.lockTimer);
    if (this.ro) {
      this.ro.disconnect();
    } else if (this.boundResize) {
      window.removeEventListener('resize', this.boundResize);
    }
    this.root.removeEventListener('keydown', this.boundKeydown);
    this.root.removeEventListener('mouseenter', this.boundEnter);
    this.root.removeEventListener('mouseleave', this.boundLeave);
    this.root.removeEventListener('focusin', this.boundFocus);
    this.root.removeEventListener('focusout', this.boundBlur);
    instances.delete(this.root);
    delete this.root.__mcInstance;
  };

  function mount(el) {
    var existing = el.__mcInstance;
    if (existing) {
      existing.destroy();
    }
    var inst = new ModernCarousel(el);
    el.__mcInstance = inst;
    instances.set(el, inst);
  }

  function scan(scope) {
    var nodes = (scope || document).querySelectorAll('.modern-carousel');
    Array.prototype.forEach.call(nodes, mount);
  }

  function sweepRemoved(root) {
    if (!root.querySelectorAll) {
      return;
    }
    if (root.matches && root.matches('.modern-carousel') && root.__mcInstance) {
      root.__mcInstance.destroy();
    }
    Array.prototype.forEach.call(root.querySelectorAll('.modern-carousel'), function (el) {
      if (el.__mcInstance) {
        el.__mcInstance.destroy();
      }
    });
  }

  if ('MutationObserver' in window) {
    var mo = new MutationObserver(function (mutations) {
      for (var m = 0; m < mutations.length; m++) {
        var mut = mutations[m];
        Array.prototype.forEach.call(mut.removedNodes, sweepRemoved);
        Array.prototype.forEach.call(mut.addedNodes, function (node) {
          if (node.nodeType !== 1) {
            return;
          }
          if (node.matches && node.matches('.modern-carousel')) {
            mount(node);
          }
          scan(node);
        });
      }
    });
    document.addEventListener('DOMContentLoaded', function () {
      mo.observe(document.body, { childList: true, subtree: true });
    });
  }

  if (window.jQuery) {
    window.jQuery(window).on('elementor/frontend/init', function () {
      try {
        if (window.elementorFrontend && window.elementorFrontend.hooks) {
          window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/Elem_Modern_Carousel.default',
            function ($scope) {
              $scope.find('.modern-carousel').each(function (_, el) {
                mount(el);
              });
            }
          );
        }
      } catch (err) {}
    });
  }

  function boot() {
    scan(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  window.ModernCarousel = ModernCarousel;
})();
