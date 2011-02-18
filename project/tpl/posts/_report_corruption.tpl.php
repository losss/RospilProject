<div class="report" id="reportarea">
    <div class="right w370 help">
        <h3>Обратите внимание!</h3>
        <ol class="numbered top20 smaller">
            <li>Ссылка на страницу госзаказа должна вести только на страницу госзаказа.</li>
            <li>Дата проведения конкурса должна быть как минимум через 3-4 недели, чтобы мы могли подготовиться.</li>
            <li>Мы не можем работать с прошедшими конкурсами даже если очевидно, что они попилены.</li>
            <li>В поле "Почему я считаю это махинацией" предоставьте обоснованные аргументы, а не просто "потому что ну ваще".</li>
            <li>Огромное спасибо вам за помощь и гражданскую позицию!</li>
        </ol>
    </div>
    <form name="report" id="reportform" action="" method="">
        
        <input type="hidden" name="f" value="report">

        <div class="inputbox">
            <div class="label">Ссылка на страницу госзаказа</div>
            <div class="field"><input type="text" name="link" id="link" value=""></div>
        </div>

        <div class="inputbox">
            <div class="label">Описание заказа-распила (вставьте текст со страницы источника)</div>
            <div class="field"><textarea name="description" id="description"></textarea></div>
        </div>

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Размер контракта (млн руб)</div>
                <div class="field"><input type="text" name="amount" id="amount" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Срок исполнения (в днях)</div>
                <div class="field"><input type="text" name="days" id="days" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Организация где пилят</div>
                <div class="field"><input type="text" name="org_name" id="org_name" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Дата проведения конкурса</div>
                <div class="field"><input type="text" name="scheduled" id="scheduled" value="DD.MM.YYYY" onfocus="javascript:this.value='';"></div>
            </div>

<!--

            <div class="halfbox">
                <div class="label">Контактное лицо организации</div>
                <div class="field"><input type="text" name="contact_name" id="contact_name" value=""></div>
            </div>
-->

            <div class="clear"></div>
        </div>
<!--
        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Адрес их электронной почты</div>
                <div class="field"><input type="text" name="contact_email" id="contact_email" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Их контактный телефон</div>
                <div class="field"><input type="text" name="contact_phone" id="contact_phone" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Дата проведения конкурса</div>
                <div class="field"><input type="text" name="scheduled" id="scheduled" value="DD.MM.YYYY" onfocus="javascript:this.value='';"></div>
            </div>
            <div class="halfbox">
                &nbsp;
            </div>
            <div class="clear"></div>
        </div>
-->
       <div class="inputbox">
            <div class="label">Почему я считаю это махинацией</div>
            <div class="field"><textarea name="whyfraud" id="whyfraud"></textarea></div>
        </div>

        <div class="inputbox">
            <div class="label">Введите символы с картинки</div>
            <div style="margin-left:-3px;"><?= $recaptcha; ?></div>
        </div>

        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить. Если не знаете, вставьте символ "-".</div>
        </div>

        <div class="inputbox">
            <button id="postreport" name="post">Готово!</button>
        </div>

    </form>
    
</div>


