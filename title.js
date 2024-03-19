
function toggleMenu() {
    var navbarLinks = document.getElementById("hide");
    if (navbarLinks.style.display === "flex") {
        navbarLinks.style.display = "none";
    } else {
        navbarLinks.style.display = "flex";
    }
}

function toRoot() {
    window.location.href = 'makeSale.php';
}
