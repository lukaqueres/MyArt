function toggleSidePanel(panel) {
    let content = document.querySelector(`#content-${panel}`);
    let arrow = document.querySelector(`#arrow-${panel}`);
    let button = document.querySelector(`#button-${panel}`);

    let active = content.classList.contains("show");

    let contents = document.querySelectorAll(".content-side-panel");
    contents.forEach(element => {
            element.classList.remove("show");
        });

    let arrows = document.querySelectorAll(".arrow");
    arrows.forEach(element => {
            element.classList.remove("show");
        });

    let buttons = document.querySelectorAll("#manage-side-panel button");
    buttons.forEach(element => {
        element.classList.remove("active");
    });

    if ( active ) {
        return true;
    } else {
        content.classList.add("show");
        arrow.classList.add("show");
        button.classList.add("active");
    }
}

function changeSidePanelSide() {
    let sidePanel = document.querySelector("#side-panel");
    sidePanel.classList.toggle("left");
    if (sidePanel.classList.contains("left")) {
        localStorage.setItem('side-panel-position', "left");
    } else {
        localStorage.setItem('side-panel-position', "right");
    }
}

function fetchSidePanelSide() {
    let sidePanel = document.querySelector("#side-panel");
    const sidePanelPosition = localStorage.getItem('side-panel-position');
    switch (sidePanelPosition) {
        case "left":
            sidePanel.classList.add("left");
            break;
    }

}

fetchSidePanelSide();