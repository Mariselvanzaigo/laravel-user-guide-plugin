// userguide.js
(function () {
  document.addEventListener('DOMContentLoaded', () => {
    // detect form: create or edit
    const form = document.querySelector('#userGuideCreateForm') || document.querySelector('#userGuideEditForm');
    if (!form) return;

    const fileInput = form.querySelector('#fileInput');
    const fileList = form.querySelector('#file-list');
    const existingContainer = form.querySelector('#existing-files');
    const urlFields = form.querySelector('#url-fields');
    const addUrlBtn = form.querySelector('#add-url');

    const allowedTypes = [
      'image/jpeg','image/png','image/jpg','application/pdf',
      'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'video/mp4','video/x-m4v','application/octet-stream','video/quicktime'
    ];

    let filesArray = []; // new files (File objects)
    let existingFiles = []; // paths for existing files (strings)

    /* ---------- helper: create url input row ---------- */
    function addUrlInput(value = '') {
      const row = document.createElement('div');
      row.className = 'url-row mb-2 d-flex flex-column';
      row.innerHTML = `
        <div class="d-flex gap-2">
          <input type="url" name="urls[]" class="form-control" placeholder="https://example.com" value="${value}">
          <button type="button" class="btn btn-sm btn-outline-danger remove-url">Delete</button>
        </div>
        <div class="invalid-feedback d-block" style="display:none"></div>
      `;
      const btn = row.querySelector('.remove-url');
      btn.addEventListener('click', () => row.remove());
      urlFields.appendChild(row);
      const input = row.querySelector('input[name="urls[]"]');
      // validation on change
      input.addEventListener('input', () => validateField(input));
      input.addEventListener('change', () => validateField(input));
      return row;
    }

    /* ---------- initialize URLs: if there are old values, preserve them ---------- */
    if (urlFields && urlFields.children.length === 0) {
      addUrlInput();
    }

    /* ---------- initialize existingFiles array from DOM (edit page) ---------- */
    if (existingContainer) {
      const nodes = existingContainer.querySelectorAll('[data-existing-file]');
      nodes.forEach(node => {
        const path = node.dataset.existingFile;
        if (path) existingFiles.push(path);
        // convert existing item to .file-box classes for consistent appearance
        node.classList.add('file-box');
        // ensure proper inner structure if needed
      });
      // attach delete handlers to existing files
      attachExistingDeleteHandlers();
    }

    /* ---------- validation helper ---------- */
    function validateField(input) {
      // find corresponding error element
      let errorEl = input.closest('.mb-3')?.querySelector('.invalid-feedback');
      if (!errorEl) {
        const row = input.closest('.url-row');
        if (row) errorEl = row.querySelector('.invalid-feedback');
      }
      if (!errorEl) return true;
      errorEl.style.display = 'none';
      errorEl.textContent = '';
      input.classList.remove('is-invalid');

      // required check
      if (input.required && !String(input.value || '').trim()) {
        errorEl.textContent = 'This field is required.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
        return false;
      }

      // name length
      if (input.id === 'name' && input.value.length > 256) {
        errorEl.textContent = 'Maximum 256 characters allowed.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
        return false;
      }
      // description
      if (input.id === 'description' && input.value.length > 2000) {
        errorEl.textContent = 'Maximum 2000 characters allowed.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
        return false;
      }
      // URL strict regex (require TLD)
      if (input.name === 'urls[]' && input.value.trim()) {
        const urlRegex = /^(https?:\/\/)([\w-]+\.)+[\w-]{2,}([\/\w .-]*)*\/?$/;
        if (!urlRegex.test(input.value.trim())) {
          errorEl.textContent = 'Enter valid URL (https://example.com)';
          errorEl.style.display = 'block';
          input.classList.add('is-invalid');
          return false;
        }
      }

      return true;
    }

    // attach validation handlers to existing static inputs
    form.querySelectorAll('input, select, textarea').forEach(input => {
      input.addEventListener('input', () => validateField(input));
      input.addEventListener('change', () => validateField(input));
    });

    /* ---------- file input change: append new files without removing old ones ---------- */
    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        const newFiles = Array.from(e.target.files || []);
        document.getElementById('files_error').textContent = '';

        newFiles.forEach(file => {
          // accept by MIME; browsers vary so also allow fallback octet-stream for some mp4
          if (!allowedTypes.includes(file.type) && file.type !== '') {
            // some OS might not set mime; we still allow if extension acceptable - but keep strict for security
            // To be safe, treat unknown types as allowed if extension is common (optional)
          }
          if (file.size > 20 * 1024 * 1024) {
            document.getElementById('files_error').textContent = 'File exceeds 20MB.';
            return;
          }
          // avoid duplicates by name + size
          if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
            filesArray.push(file);
          }
        });

        renderFileList();
        // reset native input so same file can be re-selected if needed
        fileInput.value = '';
      });
    }

    /* ---------- render newly selected files ---------- */
    function renderFileList() {
      if (!fileList) return;
      fileList.innerHTML = '';
      filesArray.forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'file-box';
        // icon text based on extension (simple)
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        const iconText = ext.substr(0, 3).toUpperCase();

        const meta = document.createElement('div');
        meta.className = 'file-meta';
        meta.innerHTML = `<div class="file-icon">${iconText}</div>
                          <div>
                            <span class="file-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</span>
                            <div class="file-size">${formatBytes(file.size)}</div>
                          </div>`;

        const actions = document.createElement('div');
        actions.className = 'file-actions';
        const del = document.createElement('button');
        del.type = 'button';
        del.className = 'btn btn-sm btn-outline-danger';
        del.textContent = 'Delete';
        del.addEventListener('click', () => {
          filesArray.splice(index, 1);
          renderFileList();
        });
        actions.appendChild(del);

        div.appendChild(meta);
        div.appendChild(actions);
        fileList.appendChild(div);
      });
    }

    /* ---------- format bytes ---------- */
    function formatBytes(bytes) {
      if (bytes === 0) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /* ---------- escape html for title ---------- */
    function escapeHtml(text) {
      return text.replace(/[&<>"']/g, (m) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
      })[m]);
    }

    /* ---------- attach delete handlers to existing files (edit) ---------- */
    function attachExistingDeleteHandlers() {
      if (!existingContainer) return;
      existingContainer.querySelectorAll('.remove-existing-file').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const el = e.currentTarget.closest('[data-existing-file]');
          if (!el) return;
          const path = el.dataset.existingFile;
          // append hidden input delete_files[] so controller can delete on submit
          if (form && path) {
            // avoid adding duplicate hidden inputs
            if (!form.querySelector(`input[name="delete_files[]"][value="${path}"]`)) {
              const hidden = document.createElement('input');
              hidden.type = 'hidden';
              hidden.name = 'delete_files[]';
              hidden.value = path;
              form.appendChild(hidden);
            }
          }
          // remove from DOM
          el.remove();
          // remove from existingFiles array
          existingFiles = existingFiles.filter(p => p !== path);
        });
      });
    }

    /* ---------- initial existing files rendering: ensure they conform to file-box markup ---------- */
    function normalizeExistingFilesMarkup() {
      if (!existingContainer) return;
      const children = Array.from(existingContainer.children);
      children.forEach(child => {
        const path = child.dataset.existingFile;
        if (!path) return;
        // if child is li with anchor text, rewrap to .file-box structure for consistent layout
        const name = child.querySelector('a') ? child.querySelector('a').textContent.trim() : path.split('/').pop();
        const sizeSpan = ''; // size for existing file could be fetched via HEAD or left empty
        const wrapper = document.createElement('div');
        wrapper.className = 'file-box';
        wrapper.setAttribute('data-existing-file', path);
        wrapper.innerHTML = `
          <div class="file-meta">
            <div class="file-icon">${(name.split('.').pop()||'').substr(0,3).toUpperCase()}</div>
            <div>
              <a href="${child.querySelector('a') ? child.querySelector('a').href : '#'}" target="_blank" class="file-name" title="${escapeHtml(name)}">${escapeHtml(name)}</a>
              <div class="file-size">${sizeSpan}</div>
            </div>
          </div>
          <div class="file-actions">
            <button type="button" class="btn btn-sm btn-danger remove-existing-file">Delete</button>
          </div>
        `;
        // replace child with wrapper
        existingContainer.replaceChild(wrapper, child);
      });
      // reattach delete handlers now that markup changed
      attachExistingDeleteHandlers();
    }

    normalizeExistingFilesMarkup();

    /* ---------- submit handler (AJAX) ---------- */
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      let valid = true;
      // validate all fields
      form.querySelectorAll('input, select, textarea').forEach(input => {
        if (!validateField(input)) valid = false;
      });

      // validate filesArray sizes/types
      for (const f of filesArray) {
        if (!allowedTypes.includes(f.type) && f.type !== '') {
          // We won't block unknown type unless extension check required
        }
        if (f.size > 20 * 1024 * 1024) valid = false;
      }

      if (!valid) return;

      // build FormData: includes regular inputs + files + delete_files[] hidden inputs appended by existing delete actions
      const fd = new FormData(form);
      filesArray.forEach(f => fd.append('files[]', f));

      fetch(form.action, {
        method: form.method || 'POST',
        body: fd,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name=_token]')?.value || '',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async res => {
        // if validation errors
        if (res.status === 422) {
          const json = await res.json();
          throw json;
        }
        const data = await res.json();
        // if backend requests redirect, store message and navigate
        if (data.redirect) {
          sessionStorage.setItem('userGuideSuccess', data.message || 'Saved successfully!');
          window.location.href = data.redirect;
          return;
        }
        // fallback: show toastr and reset
        if (data.message) toastr.success(data.message);
        // reset form UI
        form.reset();
        filesArray = [];
        renderFileList();
        if (urlFields) {
          urlFields.innerHTML = '';
          addUrlInput();
        }
      })
      .catch(err => {
        if (err && err.errors) {
          // show field-level errors
          Object.keys(err.errors).forEach(key => {
            if (key.includes('.')) {
              // dynamic field like urls.0
              const input = form.querySelector(`[name="${key.replace(/\.\d+/, '[]')}"]`);
              if (input) {
                const row = input.closest('.url-row');
                const errorDiv = row ? row.querySelector('.invalid-feedback') : null;
                if (errorDiv) {
                  errorDiv.style.display = 'block';
                  errorDiv.textContent = err.errors[key][0];
                } else {
                  // fallback: toastr
                  toastr.error(err.errors[key][0]);
                }
              }
            } else {
              const el = document.getElementById(`${key}_error`);
              if (el) {
                el.style.display = 'block';
                el.textContent = err.errors[key][0];
                const input = form.querySelector(`[name="${key}"]`);
                input?.classList.add('is-invalid');
              }
            }
          });
          return;
        }
        console.error(err);
        toastr.error('Something went wrong. Try again.');
      });
    });

    /* ---------- utility: attach add-url button ---------- */
    if (addUrlBtn) {
      addUrlBtn.addEventListener('click', () => addUrlInput());
    }

  }); // DOMContentLoaded
})();
