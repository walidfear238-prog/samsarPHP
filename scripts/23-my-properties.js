(function(){
  'use strict';
  const props = [
    { id: 1, title: 'Villa Tazri', city: 'Palmeraie · Marrakech', status: 'Published', price: '12,400,000 MAD', views: 284, leads: 14, listed: 'Oct 28', img: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=200&q=80' },
    { id: 2, title: 'Riad Yasmine', city: 'Medina · Marrakech', status: 'Published', price: '5,200,000 MAD', views: 211, leads: 9, listed: 'Oct 12', img: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=200&q=80' }
  ];

  const body = document.getElementById('prop-body');
  const modal = document.getElementById('delete-modal');
  let deleteId = null;

  function render() {
    body.innerHTML = props.map(p => `
      <tr>
        <td><input type="checkbox"/></td>
        <td>
          <div class="prop-cell">
            <img src="${p.img}" alt="${p.title}"/>
            <div><strong>${p.title}</strong><span>${p.city}</span></div>
          </div>
        </td>
        <td><span class="status-pill">${p.status}</span></td>
        <td><strong>${p.price}</strong></td>
        <td>${p.views}</td>
        <td>${p.leads}</td>
        <td>${p.listed}</td>
        <td>
          <div class="row-actions">
            <a href="03-property-details.php" class="r-btn">View</a>
            <a href="edit-property.php?id=${p.id}" class="r-btn edit">Edit</a>
            <button class="r-btn delete" onclick="openDelete(${p.id})">✕</button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  window.openDelete = (id) => {
    deleteId = id;
    modal.classList.add('is-open');
  };

  document.getElementById('cancel-delete').addEventListener('click', () => modal.classList.remove('is-open'));
  document.getElementById('confirm-delete').addEventListener('click', () => {
    // Implement delete logic
    modal.classList.remove('is-open');
    alert('Property deleted');
  });

  render();
})();
