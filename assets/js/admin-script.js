
console.log('admin chargé depuis le plugin de log');
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector(".log-search");
    const searchBtn = document.querySelector('.btn-search');
    const originalLog = document.querySelector("pre").innerText;
    searchBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const term = searchInput.value.toLowerCase(); // récupère le mot-clé recherché en minuscules
        console.log(term);
        const pre = document.querySelector("pre");
        console.log(pre);
        if (term === "") {
            pre.innerText = originalLog;
            return;
        }
        // découpe le contenu original ligne par ligne
        const lines = originalLog.split("\n");
        // // filtre les lignes contenant le mot recherché (insensible à la casse)
        const filtered = lines.filter(line => line.toLowerCase().includes(term));
        pre.innerText = filtered.join("\n");
    })

})

