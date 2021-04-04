<div class="userForm">
    <form method="<?php Conf::echoFormMethod() ?>">
        <input type="text" name="controller" value="user" hidden/>
        <input type="text" name="action" value="sentMail" hidden/>
        <input type="text" name="target" value="<?php echo  Conf::get('target'); ?>" hidden/>
        <div>
            <label>Sujet</label>
            <input type="text" name="subject" placeholder="Voici le sujet de ce mail" required/>
        </div>
        <div class="mailDescription">
            <label>Description</label>
            <textarea type="text" name="description" placeholder="Voici la description de mon mail" required></textarea>
        </div>
        <div>
            <button type="submit">Envoyer</button>
        </div>
    </form>
</div>