<?php
    use Dependencies\HTMLObject;
    use Dependencies\Session;
?>

<link rel="stylesheet" href="./public/resources/css/sidePanel.css">

<div id="side-panel">
    <div id="manage-side-panel">
        <button id="button-log-in" onclick="toggleSidePanel('log-in')"><ion-icon name="log-in-outline"></ion-icon></button>
        <button id="button-settings" onclick="toggleSidePanel('settings')"><ion-icon name="settings-outline"></ion-icon></button>
        <a href="unauthorize" class="log-out"><ion-icon name="exit-outline"></ion-icon></a>
    </div>
    <div class="arrows-side-panel">
        <div id="arrow-log-in" class="arrow"></div>
        <div id="arrow-settings" class="arrow"></div>
        <div id="arrow-exit" class="arrow"></div>
    </div>
    <div id="content-log-in" class="content-side-panel">
        <form action="authorize" method="post" novalidate onsubmit="return loginValidate(event)">
            <label for="email">Email:</label>
            <?php $email = Session::getFlash("email"); ?>
            <input name="email" type="email" placeholder="Email" <?php print ($email)?"value = {$email}":"" ?> >
            <label for="password">Password:</label>
            <input name="password" type="password" placeholder="Password">
            <input type="submit" value="Log In">
        </form>
        <div id="error">
            <?php
                $error = Session::getFlash("error");
                if ( $error ) {
                    print new HTMLObject(tag: "div", text: $error, classList: ["alert"]);
                }
            ?>
        </div>
    </div>
    <div id="content-settings" class="content-side-panel">
        <div id="side-menu-change-side-container" class="flex"><p>Change menu side</p><button onclick="changeSidePanelSide()" id="side-menu-change-side" class="ionic-clear"><ion-icon name="move-outline"></ion-icon></button></div>
    </div>
    <div id="content-exit" class="content-side-panel">
        Exit
    </div>
</div>
<script src="./public/resources/js/sidePanel.js"></script>
<script src="./public/resources/js/form.js"></script>
<script>
    function loginValidate(e) { // {{-- - This function validates login form. Must return `true` to send request - --}}
        const form = new Form(e.target, 'error');
        return form.validate({
            'email': ['trim', 'required', 'email'],
            'password': ['required'],
        });
    }
</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>