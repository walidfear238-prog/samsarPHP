(function () {
  'use strict';
  
  // Cursor effects
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(pointer: fine)').matches;
  
  if (fine && !reduced) {
    const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
    const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
    addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY }, { passive: true });
    (function loop() {
      rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18; dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
      r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`; d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
      requestAnimationFrame(loop)
    })();
    document.querySelectorAll('a,button,input,select,textarea').forEach(el => {
      el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
      el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
    });
  }

  // Nav scroll effect
  const nav = document.querySelector('.nav');
  addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });

  // Gallery setup
  function setupGallery(mainImg, thumbContainer) {
    if (!mainImg || !thumbContainer) return;
    
    const thumbs = thumbContainer.querySelectorAll('.g-thumb');
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', function() {
        thumbs.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        
        const img = this.querySelector('img');
        if (img && mainImg) {
          mainImg.style.opacity = '0';
          setTimeout(() => { 
            mainImg.src = img.src; 
            mainImg.style.opacity = '1' 
          }, 200);
        }
      });
    });
  }

  // Reveals
  const io = new IntersectionObserver((es, o) => es.forEach(e => { 
    if (e.isIntersecting) { 
      const d = e.target.dataset.delay || 0; 
      e.target.style.transitionDelay = d + 'ms'; 
      e.target.classList.add('is-in'); 
      o.unobserve(e.target) 
    } 
  }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
  
  // Wait for DOM to be ready before observing
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
  });

  // Save button
  const saveBtn = document.getElementById('detail-save-btn');
  if (saveBtn) {
    saveBtn.addEventListener('click', function(e) {
      e.preventDefault();
      const svg = this.querySelector('svg');
      const textSpan = this.querySelector('span');
      
      if (this.classList.toggle('saved')) {
        this.style.background = 'var(--crimson)';
        this.style.color = '#fff';
        this.style.borderColor = 'var(--crimson)';
        if (svg) svg.setAttribute('fill', 'currentColor');
        if (textSpan) textSpan.textContent = ' Saved';
      } else {
        this.style.background = '';
        this.style.color = '';
        this.style.borderColor = '';
        if (svg) svg.setAttribute('fill', 'none');
        if (textSpan) textSpan.textContent = ' Save';
      }
    });
  }

  // Follow button
  document.querySelectorAll('[data-follow]').forEach(btn => {
    btn.addEventListener('click', function() {
      const isFollowing = this.classList.toggle('following');
      this.textContent = isFollowing ? 'Following' : 'Follow';
      if (isFollowing) { 
        this.style.background = 'var(--crimson)'; 
        this.style.color = '#fff'; 
        this.style.borderColor = 'var(--crimson)'; 
      } else { 
        this.style.background = ''; 
        this.style.color = ''; 
        this.style.borderColor = ''; 
      }
    });
  });

  // Contact form
  const form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const b = this.querySelector('button[type="submit"]');
      if (b) {
        const o = b.textContent;
        b.textContent = 'Sent ✓'; 
        b.disabled = true;
        setTimeout(() => { 
          b.textContent = o; 
          b.disabled = false; 
          this.reset() 
        }, 1800);
      }
    });
  }

  // Book a Visit
  const bookVisitBtn = document.getElementById('book-visit-btn');
  if (bookVisitBtn) {
    bookVisitBtn.addEventListener('click', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const propertyId = urlParams.get('id');
      if (propertyId) {
        window.location.href = `09-book-visit.php?property_id=${propertyId}`;
      } else {
        alert('Please select a property to book a visit.');
      }
    });
  }

  // Load property details
  async function loadPropertyDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const propertyId = urlParams.get('id');
    
    console.log('Property ID:', propertyId);
    
    if (!propertyId) {
      const container = document.querySelector('main .container');
      if (container) {
        container.innerHTML = `
          <div style="text-align:center;padding:80px 20px;">
            <h2 style="font-size:28px;margin-bottom:20px;">Property Not Found</h2>
            <p style="color:#666;margin-bottom:30px;">No property ID specified.</p>
            <a href="02-properties.php" style="display:inline-block;padding:12px 30px;background:#1a1a1a;color:#fff;text-decoration:none;border-radius:4px;">View All Properties</a>
          </div>
        `;
      }
      return;
    }

    try {
      // FIXED: Correct path to API
      const response = await fetch(`../api/property-details.php?id=${propertyId}`);
      
      console.log('Response status:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const property = await response.json();
      console.log('Property data:', property);
      
      if (property.error) {
        throw new Error(property.error);
      }
      
      populatePropertyDetails(property);
      
    } catch (error) {
      console.error('Error loading property:', error);
      const container = document.querySelector('main .container');
      if (container) {
        // Keep the breadcrumb
        const crumbs = container.querySelector('.crumbs');
        container.innerHTML = '';
        if (crumbs) container.appendChild(crumbs);
        
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = 'text-align:center;padding:60px 20px;';
        errorDiv.innerHTML = `
          <h2 style="font-size:24px;margin-bottom:15px;color:#dc3545;">Unable to Load Property</h2>
          <p style="color:#666;margin-bottom:10px;">${error.message}</p>
          <p style="color:#999;font-size:14px;">Make sure the API path is correct and the property exists.</p>
          <a href="02-properties.php" style="display:inline-block;margin-top:20px;padding:12px 30px;background:#1a1a1a;color:#fff;text-decoration:none;border-radius:4px;">Back to Properties</a>
        `;
        container.appendChild(errorDiv);
      }
    }
  }

  function populatePropertyDetails(property) {
    console.log('Populating property:', property);
    
    // Title
    document.title = `${property.title || 'Property'} · SAMSAR`;
    
    // Breadcrumb
    const breadcrumb = document.getElementById('breadcrumb-title');
    if (breadcrumb && property.title) breadcrumb.textContent = property.title;

    // Hero
    const eyebrow = document.getElementById('property-location');
    if (eyebrow) {
      const parts = [];
      if (property.city) parts.push(property.city);
      if (property.district) parts.push(property.district);
      eyebrow.textContent = parts.length > 0 ? parts.join(' · ') : 'Morocco';
    }
    
    const titleEl = document.getElementById('property-title');
    if (titleEl && property.title) titleEl.textContent = property.title;
    
    const metaLine = document.getElementById('property-meta');
    if (metaLine) {
      const parts = [];
      if (property.bedrooms) parts.push(`${property.bedrooms} bedrooms`);
      if (property.bathrooms) parts.push(`${property.bathrooms} bathrooms`);
      if (property.area) parts.push(`${property.area} m²`);
      if (property.property_type) parts.push(property.property_type);
      metaLine.textContent = parts.length > 0 ? parts.join(' · ') : 'Luxury property';
    }
    
    const priceEl = document.getElementById('property-price');
    if (priceEl && property.price) {
      const formatted = new Intl.NumberFormat('en-US').format(property.price);
      priceEl.innerHTML = `${formatted} <small>MAD</small>`;
    }

    // Gallery
    const mainImg = document.getElementById('g-main-img');
    const thumbContainer = document.getElementById('g-thumbs');
    
    if (mainImg && property.main_image) {
      mainImg.src = property.main_image;
      mainImg.alt = property.title || 'Property';
    } else if (mainImg && property.images && property.images.length > 0) {
      mainImg.src = property.images[0];
    }
    
    if (thumbContainer && property.images && property.images.length > 0) {
      thumbContainer.innerHTML = '';
      property.images.forEach((img, index) => {
        const thumb = document.createElement('button');
        thumb.className = `g-thumb ${index === 0 ? 'active' : ''}`;
        thumb.innerHTML = `<img src="${img}" alt="${property.title} - image ${index + 1}" loading="lazy">`;
        thumbContainer.appendChild(thumb);
      });
      setupGallery(mainImg, thumbContainer);
    }

    // Key Facts
    const factBedrooms = document.getElementById('fact-bedrooms');
    if (factBedrooms) factBedrooms.textContent = property.bedrooms || '-';
    
    const factBathrooms = document.getElementById('fact-bathrooms');
    if (factBathrooms) factBathrooms.textContent = property.bathrooms || '-';
    
    const factArea = document.getElementById('fact-area');
    if (factArea) factArea.textContent = property.area ? `${property.area} m²` : '-';
    
    const factType = document.getElementById('fact-type');
    if (factType) factType.textContent = property.property_type || '-';
    
    const factStatus = document.getElementById('fact-status');
    if (factStatus) {
      const statusMap = {
        'available': 'Available',
        'rented': 'Rented',
        'sold': 'Sold',
        'pending': 'Pending',
        'draft': 'Draft'
      };
      factStatus.textContent = statusMap[property.status] || property.status || '-';
      factStatus.style.color = property.status === 'available' ? 'green' : 
                               property.status === 'sold' ? 'red' : 
                               property.status === 'rented' ? 'orange' : 'inherit';
    }
    
    const factDate = document.getElementById('fact-date');
    if (factDate && property.created_at) {
      const date = new Date(property.created_at);
      factDate.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Description
    const descContainer = document.getElementById('property-description');
    if (descContainer && property.description) {
      descContainer.innerHTML = '';
      const paragraphs = property.description.split('\n').filter(p => p.trim());
      if (paragraphs.length > 0) {
        paragraphs.forEach(p => {
          const pEl = document.createElement('p');
          pEl.textContent = p.trim();
          descContainer.appendChild(pEl);
        });
      } else {
        descContainer.innerHTML = '<p>No description available.</p>';
      }
    }

    // Features
    const featuresList = document.getElementById('property-features');
    if (featuresList) {
      featuresList.innerHTML = '';
      const features = [];
      
      if (property.bedrooms) features.push(`${property.bedrooms} Bedrooms`);
      if (property.bathrooms) features.push(`${property.bathrooms} Bathrooms`);
      if (property.area) features.push(`${property.area} m² Area`);
      if (property.property_type) features.push(`Type: ${property.property_type}`);
      if (property.status) features.push(`Status: ${property.status.charAt(0).toUpperCase() + property.status.slice(1)}`);
      if (property.city) features.push(`Location: ${property.city}`);
      
      if (features.length === 0) {
        features.push('No features listed');
      }
      
      features.forEach(feature => {
        const li = document.createElement('li');
        li.textContent = feature;
        featuresList.appendChild(li);
      });
    }

    // Agent
    const agentImg = document.getElementById('agent-avatar');
    if (agentImg) {
      agentImg.src = property.agent_avatar || 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80';
      agentImg.alt = property.agentName || 'Agent';
    }
    
    const agentName = document.getElementById('agent-name');
    if (agentName) agentName.textContent = property.agentName || 'Agency';
    
    const agentLocation = document.getElementById('agent-location');
    if (agentLocation) {
      agentLocation.textContent = property.city ? `Listing agency · ${property.city}` : 'Listing agency';
    }
    
    const agentBio = document.getElementById('agent-bio');
    if (agentBio && property.agencyName) {
      agentBio.textContent = `${property.agencyName} - Specialist in ${property.property_type || 'luxury'} properties.`;
    } else if (agentBio && property.agentName) {
      agentBio.textContent = `${property.agentName} - Professional real estate agent.`;
    } else {
      agentBio.textContent = 'Professional real estate agency.';
    }
    
    const whatsappLink = document.getElementById('whatsapp-link');
    if (whatsappLink && property.agent_phone) {
      const phone = property.agent_phone.replace(/\D/g, '');
      if (phone) {
        whatsappLink.href = `https://wa.me/${phone}`;
        whatsappLink.textContent = 'WhatsApp';
      }
    }
    
    const agencyProfileLink = document.getElementById('agency-profile-link');
    if (agencyProfileLink && property.user_id) {
      agencyProfileLink.href = `05-agency-profile.php?id=${property.user_id}`;
    }

    // Save button
    const saveBtn = document.getElementById('detail-save-btn');
    if (saveBtn && property.id) {
      saveBtn.dataset.propertyId = property.id;
    }
  }

  // Initialize - load property details when page loads
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadPropertyDetails);
  } else {
    loadPropertyDetails();
  }

})();