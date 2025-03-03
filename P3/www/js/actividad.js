// Comments {{{
const toggleCommentsBtn = document.getElementById('toggle-comments');
const commentList = document.getElementById('comments-list');
const commentPrototype = document.getElementById('comment-prototype');

toggleCommentsBtn.addEventListener('click', () => {
  commentList.classList.toggle('animate__fadeInDown');
  commentList.classList.toggle('animate__fadeOutUp');
  setTimeout(() => commentList.classList.toggle('hidden'), 
             (commentList.classList.contains('hidden') ? 0 : 750));
});


function createComment(username, email, text, time=null, avatar=null) {
  function relativeTime(previous) {
    const current = new Date();
    const msPerMinute = 60 * 1000;
    const msPerHour = msPerMinute * 60;
    const msPerDay = msPerHour * 24;
    const msPerMonth = msPerDay * 30;
    const msPerYear = msPerDay * 365;

    const elapsed = current - previous;
    if (elapsed === 0)
      return 'justo ahora';

    let str = '';

    if (elapsed < msPerMinute)
      str = 'hace ' + Math.round(elapsed/1000) + ' segundo' + ((Math.round(elapsed/1000) > 1) ? 's' : '');
    else if (elapsed < msPerHour)
      str = 'hace ' + Math.round(elapsed/msPerMinute) + ' minuto' + ((Math.round(elapsed/msPerMinute) > 1) ? 's' : '');
    else if (elapsed < msPerDay )
      str = 'hace ' + Math.round(elapsed/msPerHour ) + ' hora' + ((Math.round(elapsed/msPerHour) > 1) ? 's' : '');
    else if (elapsed < msPerMonth)
      str = 'hace ' + Math.round(elapsed/msPerDay) + ' día' + ((Math.round(elapsed/msPerDay) > 1) ? 's' : '');
    else if (elapsed < msPerYear)
      str = 'hace ' + Math.round(elapsed/msPerMonth) + ' mes' + ((Math.round(elapsed/msPerMonth) > 1) ? 'es' : '');
    else
      str = 'hace ' + Math.round(elapsed/msPerYear ) + ' año' + ((Math.round(elapsed/msPerYear) > 1) ? 's' : '');

    return str;
  }

  return {
    username: username,
    email: email,             // Not used for now
    text: text,
    time: (time === null) ? new Date() : time,
    avatar: (avatar === null) ? 'img/avatars/avatar3.jpg' : avatar,

    getHTML() {
      let comment = commentPrototype.cloneNode(true);
      comment.removeAttribute('id');
      comment.querySelector('.avatar').setAttribute('src', this.avatar);
      comment.querySelector('.nickname').innerText = this.username;
      comment.querySelector('.date').innerText = relativeTime(this.time);
      comment.querySelector('.date').setAttribute('title', this.time.toLocaleString());
      comment.querySelector('.text').innerText = this.text;

      return comment;
    },
  };
}
// }}}


// Form {{{
const form = {
  'form': document.querySelector('form'),
  'username': document.querySelector('form input[name="username"]'),
  'email': document.querySelector('form input[name="email"]'),
  'comment': document.querySelector('form textarea[name="comment"]'),
  'btn': document.querySelector('form button[type="submit"]'),
};
const array = [form['username'], form['email'], form['comment']];

form['btn'].addEventListener('click', () => {
  if (form['form'].checkValidity()) {
    const com = createComment(...array.map((x) => x.value));
    commentList.insertBefore(com.getHTML(), form['form']);
  }
  else {
    let invalid = (array.filter((x) => !x.checkValidity())).map((y) => y.name);
    showDialog(...invalid);
  }
});

form['form'].addEventListener('submit', (e) => {
  e.preventDefault();
  array.map((x) => x.value = '');
});
// }}}


// Dialog {{{
const dialog = document.querySelector('dialog');
const dialogText = document.querySelector('dialog p');
const closeDialogBtn = document.querySelector('dialog button#close');

dialog.close();

closeDialogBtn.addEventListener('click', () => {
  dialog.close();
  dialog.classList.add('hidden');
});

function showDialog() {
  let message = '';
  for (let i = 0; i < arguments.length; i++)
    message += `Error en ${arguments[i]}<br/>`;
  dialogText.innerHTML = message;
  
  dialog.showModal();
  dialog.classList.remove('hidden');
}
// }}}


// Default comments
const com1 = createComment('mfw I realize this isnt Strava', 'test1@test.com', 'Lorem ipsum dolor sit amet, qui minim labore adipisicing minim sint cillum sint consectetur cupidatat.', new Date('2024-01-24T05:39:10'), 'img/avatars/avatar1.jpg');
const com2 = createComment('mfw when I read the comment above', 'test2@test.com', '¿¿Qué dice el de arriba de lloren y sumes con dolor, que hagamos la labor mínima?? Yo sí que lloro, que se me ha roto la bicicleta :(', new Date('2024-04-01T06:10:29'), 'img/avatars/avatar2.jpg');
commentList.insertBefore(com1.getHTML(), form['form']);
commentList.insertBefore(com2.getHTML(), form['form']);

// Forbidden words
const forbiddenWords = ['fuck', 'bicicleta', 'ano', 'pedo'];
form['comment'].addEventListener('input', () => {
  let words = form['comment'].value;
  forbiddenWords.map(x => {
    if (words.includes(x))
      form['comment'].value = words.replaceAll(x, '*'.repeat(x.length));
  });
});
