window.addEventListener("load", function () {
  hideMainFromMenu();
});

function hideMainFromMenu() {
  document.querySelectorAll("#masthead .menu .menu-item a").forEach(function (e) {
    if (e.innerText.toLowerCase() === "home") {
      if (window.innerWidth > 800) {
        e.parentElement.style.display = "none";
      } else {
        e.parentElement.style.display = "block";
      }
    }
  });
}

window.addEventListener("resize", hideMainFromMenu);
