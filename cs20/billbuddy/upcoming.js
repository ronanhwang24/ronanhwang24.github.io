document.addEventListener('DOMContentLoaded', function() {
    // Fetch payments from PHPMyAdmin (simulated here)
    const payments = [
      { id: 1, name: 'Electricity Bill', dueDate: '2025-04-25' },
      { id: 2, name: 'Water Bill', dueDate: '2025-04-30' },
      // Add more payments as needed
    ];
  
    const paymentsList = document.getElementById('payments-list');
    payments.forEach(payment => {
      const paymentItem = document.createElement('div');
      paymentItem.innerHTML = `<input type="checkbox" id="payment-${payment.id}" name="payment-${payment.id}">
                               <label for="payment-${payment.id}">${payment.name} - Due: ${payment.dueDate}</label>`;
      paymentsList.appendChild(paymentItem);
    });
  
    document.getElementById('yes-button').addEventListener('click', function() {
      // Handle the 'Yes' button click
      const checkedPayments = document.querySelectorAll('#payments-list input:checked');
      if (checkedPayments.length > 0) {
        // Ensure all checked boxes are marked as checked
        checkedPayments.forEach(payment => {
          payment.checked = true;
        });
  
        // Add fireworks effect
        const fireworks = document.createElement('div');
        fireworks.id = 'fireworks';
        fireworks.style.position = 'fixed';
        fireworks.style.top = '0';
        fireworks.style.left = '0';
        fireworks.style.width = '100%';
        fireworks.style.height = '100%';
        fireworks.style.backgroundImage = "url('fireworks.gif')";
        fireworks.style.backgroundSize = 'cover';
        fireworks.style.zIndex = '9999';
        document.body.appendChild(fireworks);
  
        // Remove fireworks after a few seconds
        setTimeout(() => {
          document.body.removeChild(fireworks);
        }, 5000);
      } else {
        alert('Please check at least one payment.');
      }
    });
  
    document.getElementById('no-button').addEventListener('click', function() {
      // Handle the 'No' button click
      alert('Make sure you do them on time!!!');
    });
  });
  