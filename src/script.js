const closebtn = document.getElementById("closebtn");
closebtn.addEventListener("click", hideAlert);

function hideAlert() {
  this.parentElement.style.display = "none";
}
