<link rel="stylesheet" href="./public/resources/css/sidePanel.css">

<div id="side-panel">
    <div id="manage-side-panel">
        <button id="button-albums" onclick="toggleSidePanel('albums')"><ion-icon name="albums-outline"></ion-icon></button>
        <button id="button-users" onclick="toggleSidePanel('users')"><ion-icon name="people-outline"></ion-icon></button>
        <button id="button-settings" onclick="toggleSidePanel('settings')"><ion-icon name="settings-outline"></ion-icon></button>
        <a href="./unauthorize" class="log-out"><ion-icon name="exit-outline"></ion-icon></a>
    </div>
    <div class="arrows-side-panel">
        <div id="arrow-albums" class="arrow"></div>
        <div id="arrow-users" class="arrow"></div>
        <div id="arrow-settings" class="arrow"></div>
        <div id="arrow-exit" class="arrow"></div>
    </div>
    <div id="content-albums" class="content-side-panel">
        Articles
    </div>
    <div id="content-users" class="content-side-panel">
        Users
    </div>
    <div id="content-settings" class="content-side-panel">
        <div id="side-menu-change-side-container" class="flex"><p>Change menu side</p><button onclick="changeSidePanelSide()" id="side-menu-change-side" class="ionic-clear"><ion-icon name="move-outline"></ion-icon></button></div>
    </div>
</div>
<script src="./public/resources/js/sidePanel.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>