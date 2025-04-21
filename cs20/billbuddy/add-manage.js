document.addEventListener('DOMContentLoaded', function() {
    const selectDatabase = document.getElementById('select-database');
    const yesButton = document.getElementById('yes-button');
    const noButton = document.getElementById('no-button');
    const paymentsList = document.getElementById('payments-list');
  
    // Populate dropdown with names from the database
    fetch('/api/getNames')
      .then(response => response.json())
      .then(data => {
        data.names.forEach(name => {
          const option = document.createElement('option');
          option.value = name;
          option.textContent = name;
          selectDatabase.appendChild(option);
        });
      });
  
    // Load existing item details when selected
    selectDatabase.addEventListener('change', function() {
      const selectedName = selectDatabase.value;
      fetch(`/api/getDetails?name=${selectedName}`)
        .then(response => response.json())
        .then(data => {
          // Populate fields with data
          // Example: paymentsList.innerHTML = data.details;
        });
    });
  
    // Update existing item
    yesButton.addEventListener('click', function() {
      const selectedName = selectDatabase.value;
      const details = {}; // Gather details from form fields
      fetch(`/api/updateDetails?name=${selectedName}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(details)
      })
      .then(response => response.json())
      .then(data => {
        alert('Details updated successfully!');
      });
    });
  
    // Add new item
    noButton.addEventListener('click', function() {
      const details = {}; // Gather details from form fields
      fetch('/api/addDetails', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(details)
      })
      .then(response => response.json())
      .then(data => {
        alert('New item added successfully!');
      });
    });
  });
  