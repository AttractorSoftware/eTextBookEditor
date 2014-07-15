<div class="templates" style="display: none">
    <div class="template" name="module">
        <module>
            <module-title>
                <edit-element>Название модуля</edit-element>
                <view-element class="module-element"></view-element>
            </module-title>
            <module-background-image>&nbsp;</module-background-image>
            <module-questions>
                <view-element class="module-element"></view-element>
            </module-questions>
            <module-description>
                <view-element class="module-element">Описание модуля</view-element>
            </module-description>
            <blocks></blocks>
        </module>
    </div>

    <div class="template" name="addModuleButton">
        <add-module-button>
            <wrap>
                <a href="#" class="add-module">Добавить модуль</a>
            </wrap>
        </add-module-button>
    </div>

    <div class="template" name="moduleControlPanel">
        <control-panel class="module-panel">
            <item class="edit" title="Редактировать"><span class="glyphicon glyphicon-pencil"></span></item>
            <item class="duplicate" title="Дублировать"><span class="glyphicon glyphicon-repeat"></span></item>
            <item class="remove" title="Удалить"><span class="glyphicon glyphicon-trash"></span></item>
        </control-panel>
    </div>

    <div class="template" name="block">
        <block>
            <block-headline>
                <block-index>&nbsp;</block-index>
                <block-categories></block-categories>
                <block-title>
                    <edit-element>Название задания</edit-element>
                    <view-element></view-element>
                </block-title>
            </block-headline>
            <block-content>
                <widget>
                    <widget-content></widget-content>
                </widget>
            </block-content>
        </block>
    </div>

    <div class="template" name="addBlockButton">
        <add-block-button>
            <wrap>
                <button
                    class="add-block btn btn-primary btn-xs popover-dismiss"
                    data-toggle="popover"
                    title="Блок задание"
                    data-content="<img src='/img/task-sample.png'> <div class='popover-text'>Задания представляют из себя текст задания и управляющие инструменты, которые вы сможете добавить после того как добавите блок задания.</div>"
                    data-trigger="hover"
                    data-html="true"
                    data-placement="top"
                >
                    <span class="glyphicon glyphicon-ok-sign"></span>
                    Добавить задание
                </button>
                <button
                    class="add-rule btn btn-primary btn-xs popover-dismiss"
                    data-toggle="popover"
                    title="Блок правило"
                    data-content="<img src='/img/rule-sample.png'> <div class='popover-text'>Правило представляет из себя обычный текст, помещенный в зеленный прямоугольник. Используется для заострения внимания на вызказывание.</div>"
                    data-trigger="hover"
                    data-html="true"
                    data-placement="top"
                >
                    <span class="glyphicon glyphicon-info-sign"></span>
                    Добавить правило
                </button>
            </wrap>
            <span class="label label-primary trigger">Добавить блок</span>
        </add-block-button>
    </div>

    <div class="template" name="blockControlPanel">
        <control-panel>
            <item class="edit" title="Редактировать"><span class="glyphicon glyphicon-pencil"></span></item>
            <item class="duplicate" title="Дублировать"><span class="glyphicon glyphicon-repeat"></span></item>
            <item class="remove" title="Удалить"><span class="glyphicon glyphicon-trash"></span></item>
        </control-panel>
    </div>

    <div class="template" name="audioWidget">
        <audio-list></audio-list>
        <audio-description><view-element>Вопросы к аудио записи</view-element></audio-description>
    </div>

    <div class="template" name="audioItem">
        <audio-item>
            <video controls preload="none">
                <source src="<%= path %>" type="audio/mpeg">
            </video>
            <edit-element class="glyphicon glyphicon-remove"></edit-element>
        </audio-item>
    </div>

    <div class="template" name="imageDescription">
        <image-description>
            <images></images>
            <descs></descs>
        </image-description>
    </div>

    <div class="template" name="logicStatementWidget">
        <logic-statement></logic-statement>
    </div>

    <div class="template" name="logicStatementItem">
        <item value="<%= value %>">
            <view-element><%= text %></view-element>
            <edit-element class="text"><input type="text" value="<%= text %>"></edit-element>
            <edit-element class="value">
                <select>
                    <option value="0">ката</option>
                    <option value="1">тура</option>
                </select>
            </edit-element>
            <edit-element class="remove glyphicon glyphicon-remove"></edit-element>
        </item>
    </div>

    <div class="template" name="logicStatementAddInput">
        <edit-element class="new-statement">
            <input type="text">
            <select>
                <option value="0">ката</option>
                <option value="1">тура</option>
            </select>
            <add class="glyphicon glyphicon-plus"></add>
        </edit-element>
    </div>

    <div class="template" name="questionWidget">
        <question>
            <view-element>Текст задания</view-element>
        </question>
    </div>

    <div class="template" name="translateComparativeWidget">
        <translate-comparative>
            <list></list>
            <answers></answers>
        </translate-comparative>
    </div>

    <div class="template" name="videoWidget">
        <video-list></video-list>
        <video-description>
            <view-element>Описание к видео</view-element>
        </video-description>
    </div>

    <div class="template" name="videoItem">
        <video-item>
            <video controls preload="none">
                <source src="<%= path %>">
            </video>
            <edit-element class="glyphicon glyphicon-remove"></edit-element>
        </video-item>
    </div>

    <div class="template" name="checkEndingsWidget">
        <div class="check-endings">
            <div class="words">
                <div class="title">Слова</div>
                <div class="list"></div>
            </div>
            <div class="endings-list"></div>
        </div>
    </div>

    <div class="template" name="checkEndingsWordItem">
        <div class="item">
            <view-element></view-element>
            <edit-element>
                <input type="text" value="<%= value %>">
                <select></select>
            </edit-element>
            <edit-element class="glyphicon glyphicon-remove"></edit-element>
        </div>
    </div>

    <div class="template" name="checkEndingsEndingItem">
        <div class="item">
            <edit-element>
                <input type="text" value="<%= value %>">
            </edit-element>
            <edit-element class="glyphicon glyphicon-remove"></edit-element>
        </div>
    </div>

</div>