const nodes = [...document.querySelectorAll(".node")];

const detailsTitle          = document.getElementById("detailsTitle");
const detailsDescription    = document.getElementById("detailsDescription");
const metaSubWPL            = document.getElementById("metaSubWPL");
const metaStatus            = document.getElementById("metaStatus");
const metaPhase             = document.getElementById("metaPhase");
const metaDevelopment       = document.getElementById("metaDevelopment");
const metaTests             = document.getElementById("metaTests");
const metaProduction        = document.getElementById("metaProduction");

function clearActive() {
    nodes.forEach(node => node.classList.remove("active"));
}

function showNodeDetails(node) {
    detailsTitle.textContent        = node.dataset.title        || "Details";
    detailsDescription.textContent  = node.dataset.description  || "No description";
    metaSubWPL.textContent          = node.dataset.subwpl       || "—";
    metaStatus.textContent          = node.dataset.status       || "—";
    metaPhase.textContent           = node.dataset.phase        || "—";
    metaDevelopment.textContent     = (node.dataset.development || "0") + "%";
    metaTests.textContent           = (node.dataset.tests       || "0") + "%";
    metaProduction.textContent      = (node.dataset.production  || "0") + "%";
}

nodes.forEach(node => {
    node.addEventListener("click", () => {
        clearActive();
        node.classList.add("active");
        showNodeDetails(node);
    });
});

function downloadSVG() {
    const svg = document.getElementById("projectSvg");
    const clone = svg.cloneNode(true);
    clone.setAttribute("xmlns", "http://www.w3.org/2000/svg");

    const source =
        '<?xml version="1.0" encoding="UTF-8"?>\n' +
        new XMLSerializer().serializeToString(clone);

    const blob = new Blob([source], {
        type: "image/svg+xml;charset=utf-8"
    });

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "project-cards.svg";
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

document.getElementById("downloadSvgBtn").addEventListener("click", downloadSVG);
document.getElementById("downloadPdfBtn").addEventListener("click", () => window.print());

if (nodes.length > 0) {
    nodes[0].classList.add("active");
    showNodeDetails(nodes[0]);
}

const statusFilter = document.getElementById("statusFilter");

if (statusFilter) {
    statusFilter.addEventListener("change", () => {

        const value = statusFilter.value.toLowerCase();

        nodes.forEach(node => {

            const status = (node.dataset.status || "").toLowerCase();

            if (value === "all" || status.includes(value)) {
                node.style.display = "";
            } else {
                node.style.display = "none";
            }

        });

    });
}