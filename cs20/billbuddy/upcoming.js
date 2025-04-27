document.addEventListener('DOMContentLoaded', function() {
  const yesButton = document.getElementById('yes-button');
  const paymentsForm = document.getElementById('payments-form');

  yesButton.addEventListener('click', function(event) {
      event.preventDefault(); // Stop the form from submitting immediately

      const checkedPayments = document.querySelectorAll('#payments-list input:checked');
      if (checkedPayments.length > 0) {
          // Fireworks effect
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

          // Wait a few seconds, then submit form
          setTimeout(() => {
              document.body.removeChild(fireworks);
              paymentsForm.submit(); // Now actually submit the form
          }, 3000); // show fireworks for 3 seconds
      } else {
          alert('Please check at least one payment.');
      }
  });
});