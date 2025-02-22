document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".upgrade-button").forEach(button => {
        button.addEventListener("click", function () {
            let buildingId = this.dataset.buildingId;
            let button = this;

            fetch("../pages/upgrade_building.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `building_id=${buildingId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    let endTime = data.end_time;
                    button.disabled = true;
                    startCountdown(button, endTime);
                } else {
                    alert(data.message);
                }
            });
        });
    });
});

function startCountdown(button, endTime) {
    let interval = setInterval(() => {
        let now = Math.floor(Date.now() / 1000);
        let remaining = endTime - now;

        if (remaining <= 0) {
            clearInterval(interval);
            button.textContent = "Améliorer";
            button.disabled = false;
            location.reload(); // Recharge la page pour afficher le nouveau niveau
        } else {
            button.textContent = `Amélioration... ${remaining}s`;
        }
    }, 1000);
}
