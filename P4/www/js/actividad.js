// Comment section {{{

const toggleCommentsBtn = document.getElementById("toggle-comments");
const commentListDiv = document.getElementById("comments-list");

toggleCommentsBtn.addEventListener("click", () => {
  commentListDiv.classList.toggle("animate__fadeInDown");
  commentListDiv.classList.toggle("animate__fadeOutUp");
  setTimeout(() => commentListDiv.classList.toggle("hidden"), 
             (commentListDiv.classList.contains("hidden") ? 0 : 750));
});

// }}}


// Format comments correctly {{{

const commentList = document.querySelectorAll("#comments-list > [id^='comment-']");

function relativeTime(previous) {
  const current = new Date();
  const msPerMinute = 60 * 1000;
  const msPerHour = msPerMinute * 60;
  const msPerDay = msPerHour * 24;
  const msPerMonth = msPerDay * 30;
  const msPerYear = msPerDay * 365;

  const elapsed = current - previous;
  if (elapsed === 0)
    return "justo ahora";

  let str = "";

  if (elapsed < msPerMinute)
    str = "hace " + Math.round(elapsed/1000) + " segundo" + ((Math.round(elapsed/1000) > 1) ? "s" : "");
  else if (elapsed < msPerHour)
    str = "hace " + Math.round(elapsed/msPerMinute) + " minuto" + ((Math.round(elapsed/msPerMinute) > 1) ? "s" : "");
  else if (elapsed < msPerDay )
    str = "hace " + Math.round(elapsed/msPerHour ) + " hora" + ((Math.round(elapsed/msPerHour) > 1) ? "s" : "");
  else if (elapsed < msPerMonth)
    str = "hace " + Math.round(elapsed/msPerDay) + " día" + ((Math.round(elapsed/msPerDay) > 1) ? "s" : "");
  else if (elapsed < msPerYear)
    str = "hace " + Math.round(elapsed/msPerMonth) + " mes" + ((Math.round(elapsed/msPerMonth) > 1) ? "es" : "");
  else
    str = "hace " + Math.round(elapsed/msPerYear ) + " año" + ((Math.round(elapsed/msPerYear) > 1) ? "s" : "");

  return str;
}

commentList.forEach((comment) => {
  const time = new Date(comment.querySelector(".date").innerHTML);
  comment.querySelector(".date").innerText = relativeTime(time);
  comment.querySelector(".date").setAttribute("title", time.toLocaleString());
});

// }}}


// Form {{{

const form = {
  "form": document.querySelector("form"),
  "username": document.querySelector("form input[name='username']"),
  "email": document.querySelector("form input[name='email']"),
  "comment": document.querySelector("form textarea[name='comment']"),
  "btn": document.querySelector("form button[type='submit']"),
};
const array = [form["username"], form["email"], form["comment"]];

form["btn"].addEventListener("click", () => {
  if (form["form"].checkValidity()) {
    const com = createComment(...array.map((x) => x.value));
    commentListDiv.insertBefore(com.getHTML(), form["form"]);
  }
  else {
    let invalid = (array.filter((x) => !x.checkValidity())).map((y) => y.name);
    showDialog(...invalid);
  }
});

form["form"].addEventListener("submit", (e) => {
  e.preventDefault();
  array.map((x) => x.value = "");
});

// }}}


// Dialog {{{

const dialog = document.querySelector("dialog");
const dialogText = document.querySelector("dialog p");
const closeDialogBtn = document.querySelector("dialog button#close");

dialog.close();

closeDialogBtn.addEventListener("click", () => {
  dialog.close();
  dialog.classList.add("hidden");
});

function showDialog() {
  let message = "";
  for (let i = 0; i < arguments.length; i++)
    message += `Error en ${arguments[i]}<br/>`;
  dialogText.innerHTML = message;
  
  dialog.showModal();
  dialog.classList.remove("hidden");
}

// }}}


// Forbidden words (client side verification) {{{

form["comment"].addEventListener("input", () => {
  let words = form["comment"].value;
  forbiddenWords.map(x => {
    if (words.includes(x))
      form["comment"].value = words.replaceAll(x, "*".repeat(x.length));
  });
});

// }}}


// Print page {{{

const printButton = document.getElementById("print-button");
printButton.addEventListener("click", (e) => {
  e.preventDefault();
  window.print();
});

// }}}
