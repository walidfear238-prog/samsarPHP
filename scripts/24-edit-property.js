(function(){
  'use strict';

  // Get ID from URL
  const urlParams = new URLSearchParams(window.location.search);
  const propId = parseInt(urlParams.get('id'));

  // Load data
  let properties = JSON.parse(localStorage.getItem('samsar_properties')) || [];
  const property = properties.find(p => p.id === propId);

  if(!property) {
    // If property not found, redirect back
    window.location.href = '23-my-properties.php';
  }

  // Fill form
  document.getElementById('prop-title').value = property.title || '';
  document.getElementById('prop-price').value = property.price || '';
  document.getElementById('prop-city').value = property.city || 'Marrakech';
  document.getElementById('prop-status').value = property.status || 'active';

  // Handle Save
  document.getElementById('edit-form').addEventListener('submit', (e) => {
    e.preventDefault();

    // Update property data
    property.title = document.getElementById('prop-title').value;
    property.price = document.getElementById('prop-price').value;
    property.city = document.getElementById('prop-city').value;
    property.status = document.getElementById('prop-status').value;
    // Note: In a real app, we'd update description, type, etc. too.

    // Save to localStorage
    const index = properties.findIndex(p => p.id === propId);
    if(index !== -1) {
      properties[index] = property;
      localStorage.setItem('samsar_properties', JSON.stringify(properties));
    }

    // Redirect back
    window.location.href = '23-my-properties.php';
  });

  // Transition logic
  requestAnimationFrame(() => document.body.classList.add('is-entering'));
})();
