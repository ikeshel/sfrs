const nodes = [...document.querySelectorAll(".node")];
const statusFilter = document.getElementById("statusFilter");

const detailsTitle = document.getElementById("detailsTitle");
const detailsDescription = document.getElementById("detailsDescription");
const metaOwner = document.getElementById("metaOwner");
const metaStatus = document.getElementById("metaStatus");
const metaPhase = document.getElementById("metaPhase");
const metaProgress = document.getElementById("metaProgress");

function clearActive() {
    nodes.forEach(node => node.classList.remove("active"));
}

function showNodeDetails(node) {
    detailsTitle.textContent = node.dataset.title || "Details";
    detailsDescription.textContent = node.dataset.description || "No description";
    metaOwner.textContent = node.dataset.owner || "—";
    metaStatus.textContent = node.dataset.status || "—";
    metaPhase.textContent = node.dataset.phase || "—";
    metaProgress.textContent = (node.dataset.progress || "0") + "%";
}

nodes.forEach(node => {
    node.addEventListener("click", () => {
        clearActive();
        node.classList.add("active");
        showNodeDetails(node);
    });
});

statusFilter.addEventListener("change", () => {
    const value = statusFilter.value;

    nodes.forEach(node => {
        const nodeStatus = node.dataset.status || "";
        const visible = value === "all" || nodeStatus === value;
        node.style.display = visible ? "" : "none";
    });

    clearActive();
    detailsTitle.textContent = "Details";
    detailsDescription.textContent = "Select a project item.";
    metaOwner.textContent = "—";
    metaStatus.textContent = "—";
    metaPhase.textContent = "—";
    metaProgress.textContent = "—";
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
    a.download = "project-chart.svg";
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