<div class="templates" style="display: none">
    <div class="template" name="module">
        <module>
            <module-title>
                <view-element class="module-element">Новый модуль</view-element>
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

    <div class="template" name="block">
        <block>
            <block-headline>
                <block-index>&nbsp;</block-index>
                <block-categories></block-categories>
                <block-title>
                    <view-element>Новый блок</view-element>
                    </block-title>
            </block-headline>
            <block-content>
                <widget>
                    <widget-content></widget-content>
                </widget>
            </block-content>
        </block>
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

</div>