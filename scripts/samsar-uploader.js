/* =====================================================
   SAMSAR · Choose Image component behavior
   Progressive UI on top of a normal <input type="file">.
   The input keeps its original name/attributes at all times,
   so existing form submissions and upload endpoints are
   completely unaffected — this file only changes what the
   user sees while picking files.
   ===================================================== */
(function () {
  'use strict';

  function tr(key, fallback) {
    return window.t ? window.t(key, fallback) : fallback;
  }

  function formatSize(bytes) {
    if (bytes >= 1024 * 1024) {
      var mb = bytes / (1024 * 1024);
      return (Math.round(mb * 10) / 10) + ' MB';
    }
    if (bytes >= 1024) {
      return Math.round(bytes / 1024) + ' KB';
    }
    return bytes + ' B';
  }

  function fileKey(f) {
    return f.name + '_' + f.size + '_' + f.lastModified;
  }

  function initUploader(root) {
    var input = root.querySelector('.su-input');
    var dropzone = root.querySelector('.su-dropzone');
    var previews = root.querySelector('.su-previews');
    var countEl = root.querySelector('.su-count');
    var warnEl = root.querySelector('.su-warning');
    if (!input || !dropzone || !previews) return;

    var maxFiles = parseInt(root.dataset.maxFiles || '0', 10) || 0;
    var maxBytes = parseInt(root.dataset.maxBytes || '0', 10) || 0;
    var acceptList = (root.dataset.accept || '')
      .split(',')
      .map(function (s) { return s.trim(); })
      .filter(Boolean);

    var files = [];
    var warnTimer = null;

    function warn(msg) {
      if (!warnEl) return;
      warnEl.textContent = msg;
      warnEl.hidden = false;
      clearTimeout(warnTimer);
      warnTimer = setTimeout(function () { warnEl.hidden = true; }, 4500);
    }

    function syncInput() {
      var dt = new DataTransfer();
      files.forEach(function (f) { dt.items.add(f); });
      input.files = dt.files;
    }

    function render() {
      previews.innerHTML = '';

      files.forEach(function (file, idx) {
        var card = document.createElement('div');
        card.className = 'su-card';

        var img = document.createElement('img');
        img.className = 'su-thumb';
        img.alt = file.name;
        var url = URL.createObjectURL(file);
        img.src = url;
        img.addEventListener('load', function () { URL.revokeObjectURL(url); });
        card.appendChild(img);

        var info = document.createElement('div');
        info.className = 'su-card-info';

        var name = document.createElement('div');
        name.className = 'su-card-name';
        name.textContent = file.name;
        name.title = file.name;
        info.appendChild(name);

        var size = document.createElement('div');
        size.className = 'su-card-size';
        size.textContent = formatSize(file.size);
        info.appendChild(size);

        card.appendChild(info);

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'su-remove';
        removeBtn.setAttribute('aria-label', 'Remove ' + file.name);
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          files.splice(idx, 1);
          syncInput();
          render();
        });
        card.appendChild(removeBtn);

        previews.appendChild(card);
      });

      if (files.length && (!maxFiles || files.length < maxFiles)) {
        var addMore = document.createElement('label');
        addMore.className = 'su-add-more';
        addMore.setAttribute('for', input.id);
        addMore.innerHTML =
          '<span class="su-add-more-plus">+</span><span>' + tr('uploader.add_more', 'Add more') + '</span>';
        previews.appendChild(addMore);
      }

      if (countEl) {
        if (files.length) {
          countEl.hidden = false;
          countEl.textContent = files.length +
            (maxFiles ? ' / ' + maxFiles : '') + ' ' +
            (files.length === 1
              ? tr('uploader.count_one', 'image selected')
              : tr('uploader.count_many', 'images selected'));
        } else {
          countEl.hidden = true;
        }
      }

      dropzone.classList.toggle('has-files', files.length > 0);
    }

    function addFiles(newFiles) {
      var existingKeys = {};
      files.forEach(function (f) { existingKeys[fileKey(f)] = true; });

      Array.from(newFiles).forEach(function (f) {
        var typeOk = acceptList.length ? acceptList.indexOf(f.type) !== -1 : f.type.indexOf('image/') === 0;
        if (!typeOk) {
          warn('"' + f.name + '" ' + tr('uploader.warn_type', 'isn\u2019t a supported image format.'));
          return;
        }
        if (maxBytes && f.size > maxBytes) {
          warn('"' + f.name + '" ' + tr('uploader.warn_size', 'is too large ({size}). Max {max}.')
            .replace('{size}', formatSize(f.size)).replace('{max}', formatSize(maxBytes)));
          return;
        }
        var key = fileKey(f);
        if (existingKeys[key]) return;
        if (maxFiles && files.length >= maxFiles) {
          warn(tr('uploader.warn_maxfiles', 'You can upload up to {max} images.').replace('{max}', maxFiles));
          return;
        }
        files.push(f);
        existingKeys[key] = true;
      });

      syncInput();
      render();
    }

    input.addEventListener('change', function () {
      if (input.files && input.files.length) addFiles(input.files);
    });

    ['dragenter', 'dragover'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('is-drag');
      });
    });

    ['dragleave', 'drop'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('is-drag');
      });
    });

    dropzone.addEventListener('drop', function (e) {
      var dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) addFiles(dt.files);
    });

    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.samsar-uploader').forEach(initUploader);
  });
})();
