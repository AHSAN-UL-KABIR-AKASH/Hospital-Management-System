/* =========================================================
   Cancer Care - Vanilla JavaScript
   No jQuery / no frameworks.
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Mobile navigation ---------- */
  var navToggle = document.getElementById('navToggle');
  var navLinks = document.getElementById('navLinks');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
  }

  /* ---------- Password show/hide ---------- */
  document.querySelectorAll('.password-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.getAttribute('data-target');
      var input = document.getElementById(targetId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
      } else {
        input.type = 'password';
        btn.textContent = 'Show';
      }
    });
  });

  /* ---------- Registration / password confirmation validation ---------- */
  var registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      var password = document.getElementById('password');
      var confirm = document.getElementById('confirm_password');
      var errorBox = document.getElementById('confirmError');
      if (password && confirm && password.value !== confirm.value) {
        e.preventDefault();
        if (errorBox) errorBox.textContent = 'Passwords do not match.';
        confirm.focus();
      } else if (password && password.value.length < 8) {
        e.preventDefault();
        if (errorBox) errorBox.textContent = 'Password must be at least 8 characters.';
      }
    });
  }

  /* ---------- Donation amount quick-select ---------- */
  var quickButtons = document.querySelectorAll('.quick-amount-btn');
  var amountInput = document.getElementById('donationAmount');
  if (quickButtons.length && amountInput) {
    quickButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        quickButtons.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var val = btn.getAttribute('data-amount');
        if (val === 'custom') {
          amountInput.value = '';
          amountInput.focus();
          amountInput.readOnly = false;
        } else {
          amountInput.value = val;
        }
      });
    });
  }

  /* ---------- Donation form validation ---------- */
  var donateForm = document.getElementById('donateForm');
  if (donateForm) {
    donateForm.addEventListener('submit', function (e) {
      var amount = parseFloat(amountInput ? amountInput.value : '0');
      var errorBox = document.getElementById('donateError');
      if (isNaN(amount) || amount < 10) {
        e.preventDefault();
        if (errorBox) errorBox.textContent = 'Please enter a valid donation amount (minimum ৳10).';
        return;
      }
      var confirmBtn = donateForm.querySelector('button[type="submit"]');
      if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';
      }
    });
  }

  /* ---------- Image preview on upload ---------- */
  document.querySelectorAll('.image-input').forEach(function (input) {
    input.addEventListener('change', function () {
      var previewId = input.getAttribute('data-preview');
      var preview = document.getElementById(previewId);
      if (!preview || !input.files || !input.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    });
  });

  /* ---------- Progress bar animation ---------- */
  document.querySelectorAll('.progress-bar-fill').forEach(function (bar) {
    var pct = bar.getAttribute('data-progress') || '0';
    setTimeout(function () {
      bar.style.width = pct + '%';
    }, 100);
  });

  /* ---------- Copy campaign link ---------- */
  document.querySelectorAll('.copy-link-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = btn.getAttribute('data-url') || window.location.href;
      navigator.clipboard.writeText(url).then(function () {
        var original = btn.textContent;
        btn.textContent = 'Link Copied!';
        setTimeout(function () { btn.textContent = original; }, 2000);
      }).catch(function () {
        alert('Copy this link: ' + url);
      });
    });
  });

  /* ---------- Search / filter UI (filter pills) ---------- */
  document.querySelectorAll('.filter-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
      document.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      var filterInput = document.getElementById('filterValue');
      var form = document.getElementById('searchForm');
      if (filterInput && form) {
        filterInput.value = pill.getAttribute('data-filter');
        form.submit();
      }
    });
  });

  /* ---------- Confirmation dialogs (modal) ---------- */
  document.querySelectorAll('[data-confirm-modal]').forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      var modalId = trigger.getAttribute('data-confirm-modal');
      var modal = document.getElementById(modalId);
      if (modal) {
        e.preventDefault();
        modal.classList.add('active');
        modal.dataset.pendingHref = trigger.getAttribute('href') || '';
        modal.dataset.pendingForm = trigger.getAttribute('data-form') || '';
      }
    });
  });
  document.querySelectorAll('.modal-cancel').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });
  document.querySelectorAll('.modal-confirm').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var modal = btn.closest('.modal-overlay');
      if (!modal) return;
      var formId = modal.dataset.pendingForm;
      var href = modal.dataset.pendingHref;
      if (formId) {
        var form = document.getElementById(formId);
        if (form) form.submit();
      } else if (href) {
        window.location.href = href;
      }
    });
  });

  /* ---------- Simple client-side "required" star validation feedback ---------- */
  document.querySelectorAll('form.js-validate').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var invalid = false;
      form.querySelectorAll('[required]').forEach(function (field) {
        if (!field.value || !field.value.trim()) {
          invalid = true;
          field.style.borderColor = '#d9534f';
        } else {
          field.style.borderColor = '';
        }
      });
      if (invalid) {
        e.preventDefault();
      }
    });
  });

  /* ---------- Loading state helper for any form ---------- */
  document.querySelectorAll('form.js-loading-state').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn && !btn.disabled) {
        btn.dataset.originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Please wait...';
      }
    });
  });

});
