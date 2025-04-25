<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dental Clinic CMS Editor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .dashboard {
      max-width: 800px;
      margin: 2rem auto;
      padding: 2rem;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .image-preview {
      max-height: 150px;
      object-fit: cover;
      margin-top: 10px;
    }
    #loadingIndicator {
      display: none;
      margin-top: 20px;
    }
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
    }
    #editorContent {
      display: none;
    }
    #initialMessage {
      text-align: center;
      padding: 2rem;
      color: #6c757d;
    }
  </style>
</head>
<body>
<div class="dashboard">
  <h2 class="mb-4">CMS Editor Dashboard</h2>

  <div class="mb-3">
    <label for="sectionSelect" class="form-label">Choose section to edit:</label>
    <select id="sectionSelect" class="form-select">
      <option value="" selected>Please select page to edit</option>
      <option value="services">Services</option>
      <option value="aboutus">About Us</option>
      <option value="contact">Contact</option>
    </select>
  </div>

  <div id="initialMessage">
    <p>Please select a page from the dropdown above to begin editing.</p>
  </div>

  <div id="editorContent">
    <div id="loadingIndicator" class="alert alert-info">
      Loading content data...
    </div>
    
    <div id="errorContainer"></div>

    <form id="editorForm">
      <div id="formFields" class="mt-3">
        <!-- Form fields will be dynamically inserted here -->
      </div>
      <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" id="refreshButton" class="btn btn-outline-secondary">Refresh Content</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast notifications -->
<div class="toast-container">
  <div id="saveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white">
      <strong class="me-auto">Success</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Content updated successfully!
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Timestamp for cache-busting
let timestamp = new Date().getTime();

// Helper function to handle image paths consistently
function getImagePath(imagePath) {
  if (!imagePath) return '';
  
  // If path already includes images/, use it directly
  if (imagePath.includes('images/')) {
    return imagePath;
  }
  // Otherwise prepend images/
  return `images/${imagePath}`;
}

// Function to extract just the filename from a path
function getImageFilename(imagePath) {
  if (!imagePath) return '';
  
  // If path includes images/, remove that prefix
  if (imagePath.includes('images/')) {
    return imagePath.replace('images/', '');
  }
  return imagePath;
}

// Helper functions for creating form elements
function createInput(label, name, value = '') {
  return `
    <div class="mb-3">
      <label class="form-label">${label}</label>
      <input type="text" class="form-control" name="${name}" value="${value}">
    </div>
  `;
}

function createTextarea(label, name, value = '') {
  return `
    <div class="mb-3">
      <label class="form-label">${label}</label>
      <textarea class="form-control" name="${name}" rows="4">${value}</textarea>
    </div>
  `;
}

function createImageInput(label, name, imagePath = '') {
  const displayPath = getImagePath(imagePath);
  const filename = getImageFilename(imagePath);
  const imgPreview = displayPath ? `<img src="${displayPath}" class="image-preview mt-2">` : '';
  
  return `
    <div class="mb-3">
      <label class="form-label">${label}</label>
      <input type="file" class="form-control" name="${name}" accept="image/*">
      ${imgPreview}
      <input type="hidden" name="existing${name}" value="${filename}">
    </div>
  `;
}

// Function to show error messages
function showError(message) {
  const errorContainer = document.getElementById('errorContainer');
  errorContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show mb-3">${message}</div>`;
}

// Function to clear error messages
function clearError() {
  const errorContainer = document.getElementById('errorContainer');
  errorContainer.innerHTML = '';
}

// Function to show success toast
function showSuccessToast() {
  const toastEl = document.getElementById('saveToast');
  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}

// Content loading function
function loadContent(section) {
  if (!section) {
    document.getElementById('initialMessage').style.display = 'block';
    document.getElementById('editorContent').style.display = 'none';
    return;
  }

  document.getElementById('initialMessage').style.display = 'none';
  document.getElementById('editorContent').style.display = 'block';
  
  const loadingIndicator = document.getElementById('loadingIndicator');
  const formFields = document.getElementById('formFields');
  
  loadingIndicator.style.display = 'block';
  formFields.innerHTML = '';
  clearError();

  // Use cache-busting timestamp
  fetch(`content.json?t=${timestamp}`)
    .then(response => {
      if (!response.ok) throw new Error(`Could not load content file (HTTP ${response.status})`);
      return response.json();
    })
    .then(data => {
      console.log("Content loaded successfully:", data);
      
      let html = '';
      
      if (section === 'services') {
        if (!data.services) throw new Error("Services data not found in JSON");
        
        data.services.forEach((item, index) => {
          html += `<div class="card mb-4 p-3">`;
          html += `<h5>Service #${index + 1}</h5>`;
          html += createInput(`Title`, `title${index}`, item.title);
          html += createTextarea(`Description`, `description${index}`, item.description);
          html += createImageInput(`Image`, `Image${index}`, item.image);
          html += `</div>`;
        });
        html += `<input type="hidden" name="count" value="${data.services.length}">`;
      } else if (section === 'aboutus') {
        if (!data.about) throw new Error("About Us data not found in JSON");

        html += `<div class="card mb-4 p-3">`;
        html += `<h5>Hero Section</h5>`;
        html += createInput("Title", "heroTitle", data.about.hero.title);
        html += createInput("Subtitle", "heroSubtitle", data.about.hero.subtitle);
        html += `</div>`;
        
        html += `<div class="card mb-4 p-3">`;
        html += `<h5>Mission & Vision</h5>`;
        html += createTextarea("Mission Statement", "mission", data.about.mission);
        html += createTextarea("Vision Statement", "vision", data.about.vision);
        html += `</div>`;
        
        if (data.about.values && data.about.values.length > 0) {
          html += `<div class="card mb-4 p-3">`;
          html += `<h5>Core Values</h5>`;
          data.about.values.forEach((value, index) => {
            html += `<div class="border-top pt-3 mt-3">`;
            html += `<h6>Value #${index + 1}</h6>`;
            html += createInput("Icon", `valueIcon${index}`, value.icon);
            html += createInput("Title", `valueTitle${index}`, value.title);
            html += createTextarea("Description", `valueDesc${index}`, value.description);
            html += `</div>`;
          });
          html += `</div>`;
        }
        
        if (data.about.team && data.about.team.length > 0) {
          html += `<div class="card mb-4 p-3">`;
          html += `<h5>Team Members</h5>`;
          data.about.team.forEach((member, index) => {
            html += `<div class="border-top pt-3 mt-3">`;
            html += `<h6>Team Member #${index + 1}</h6>`;
            html += createInput("Name", `teamName${index}`, member.name);
            html += createInput("Role", `teamRole${index}`, member.role);
            html += createTextarea("Bio", `teamBio${index}`, member.bio);
            html += createImageInput("Photo", `TeamImage${index}`, member.image);
            html += `</div>`;
          });
          html += `</div>`;
        }

        // Gallery Section
        if (data.about.gallery && data.about.gallery.length > 0) {
          html += `<div class="card mb-4 p-3">`;
          html += `<h5>Clinic Facilities (Gallery)</h5>`;
          data.about.gallery.forEach((item, index) => {
            html += `<div class="border-top pt-3 mt-3">`;
            html += `<h6>Facility #${index + 1}</h6>`;
            html += createInput("Alt Text", `galleryAlt${index}`, item.alt);
            html += createImageInput("Image", `GalleryImage${index}`, item.image);
            html += `</div>`;
          });
          html += `</div>`;
          html += `<input type="hidden" name="galleryCount" value="${data.about.gallery.length}">`;
        }

        // CTA section
        if (data.about.cta) {
          html += `<div class="card mb-4 p-3">`;
          html += `<h5>Call to Action</h5>`;
          html += createInput("CTA Text", "ctaText", data.about.cta.text);
          html += createInput("CTA Subtext", "ctaSubtext", data.about.cta.subtext);
          html += createInput("Button Label", "ctaButtonLabel", data.about.cta.button.label);
          html += createInput("Button Link", "ctaButtonLink", data.about.cta.button.link);
          html += `</div>`;
        }
      } else if (section === 'contact') {
        // New section for Contact Form editing
        if (!data.contact) throw new Error("Contact data not found in JSON");
        
        html += `<div class="card mb-4 p-3">`;
        html += `<h5>Contact Form Settings</h5>`;
        html += createInput("Header Title", "contactHeader", data.contact.header);
        html += `</div>`;
        
        html += `<div class="card mb-4 p-3">`;
        html += `<h5>Form Field Labels</h5>`;
        html += createInput("Name Field Label", "contactNameField", data.contact.fields.name);
        html += createInput("Email Field Label", "contactEmailField", data.contact.fields.email);
        html += createInput("Subject Field Label", "contactSubjectField", data.contact.fields.subject);
        html += createInput("Message Field Label", "contactMessageField", data.contact.fields.message);
        html += `</div>`;
        
        html += `<div class="card mb-4 p-3">`;
        html += `<h5>Form Button & Messages</h5>`;
        html += createInput("Submit Button Text", "contactButton", data.contact.button);
        html += createInput("Success Message", "contactSuccess", data.contact.success);
        html += createInput("Error Message", "contactError", data.contact.error);
        html += `</div>`;
      }

      loadingIndicator.style.display = 'none';
      formFields.innerHTML = html;
    })
    .catch(error => {
      loadingIndicator.style.display = 'none';
      showError(`Error loading content: ${error.message}`);
      console.error('Error details:', error);
    });
}

// Handle form submission with JSON API
document.getElementById('editorForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const submitButton = this.querySelector('button[type="submit"]');
  const originalButtonText = submitButton.textContent;
  submitButton.disabled = true;
  submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
  
  const section = document.getElementById('sectionSelect').value;
  
  // Using FormData for file uploads
  const formData = new FormData(this);
  formData.append('section', section);

  fetch('update_content.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) throw new Error(`Server error: ${response.status}`);
    return response.text();
  })
  .then(data => {
    // Update timestamp for cache busting
    timestamp = new Date().getTime();
    
    // Show success toast
    showSuccessToast();
    
    // Reload content with new data
    loadContent(section);
  })
  .catch(error => {
    showError(`Failed to save changes: ${error.message}`);
  })
  .finally(() => {
    submitButton.disabled = false;
    submitButton.textContent = originalButtonText;
  });
});

// Refresh button handler
document.getElementById('refreshButton').addEventListener('click', function() {
  timestamp = new Date().getTime(); // Update timestamp for cache busting
  loadContent(document.getElementById('sectionSelect').value);
});

document.getElementById('sectionSelect').addEventListener('change', function() {
  loadContent(this.value);
});

document.addEventListener('DOMContentLoaded', function() {
  // Don't load any section initially - just show the message
  // Initial selection is empty value
});
</script>
</body>
</html>