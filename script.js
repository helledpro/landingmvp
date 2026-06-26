const burger = document.getElementById('burger');
const nav = document.getElementById('nav');

burger?.addEventListener('click', () => {
  const isOpen = nav.classList.toggle('open');
  burger.setAttribute('aria-expanded', String(isOpen));
});

nav?.querySelectorAll('a').forEach((link) => {
  link.addEventListener('click', () => {
    nav.classList.remove('open');
    burger?.setAttribute('aria-expanded', 'false');
  });
});

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', (event) => {
    const target = document.querySelector(anchor.getAttribute('href'));
    if (!target) return;
    event.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

const form = document.getElementById('leadForm');
const message = document.getElementById('formMessage');

form?.addEventListener('submit', (event) => {
  event.preventDefault();
  const data = new FormData(form);

  if (data.get('website')) return;

  // Здесь можно подключить реальную отправку заявки в Telegram/email:
  // fetch('send.php', { method: 'POST', body: data }) или запрос к вашему API/webhook.
  message.className = 'form-message success';
  message.textContent = 'Спасибо! Заявка принята. Мы свяжемся с вами и подготовим прогноз для клиники.';
  form.reset();
});
