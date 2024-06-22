window.addEventListener('DOMContentLoaded', () => {
  const html = 'Ich bin über die E-Mail Adresse <a href="mailto:info' + '@' + 'speich.net">info' + '@' + 'speich.net</a> kontaktierbar<br>';
  document.getElementById('email').insertAdjacentHTML('afterbegin', html);
});