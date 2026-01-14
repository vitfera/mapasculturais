<?php use MapasCulturais\i; ?>

<!-- Grid Mode (Padrão) -->
<div ng-if="::field.config.viewMode === 'grid' || !field.config.viewMode" 
     class="image-vote-grid"
     ng-class="'image-size-' + (field.config.imageSize || 'medium')">
    
    <div ng-repeat="option in ::field.fieldOptions track by $index" 
         class="image-vote-option"
         ng-class="{'selected': isImageVoteOptionSelected(field, option)}"
         ng-click="toggleImageVoteOption(field, option)">
        
        <div class="image-vote-option__image" ng-if="option.imageUrl">
            <img ng-src="{{::option.imageUrl}}" 
                 alt="{{::option.label}}"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3ESem imagem%3C/text%3E%3C/svg%3E'">
        </div>
        
        <div class="image-vote-option__content">
            <div class="image-vote-option__title">{{::option.label}}</div>
            
            <div ng-if="::option.description" class="image-vote-option__description">
                {{::option.description}}
            </div>

            <div class="image-vote-option__selector">
                <input type="{{::field.config.selectionMode === 'multiple' ? 'checkbox' : 'radio'}}" 
                       ng-checked="isImageVoteOptionSelected(field, option)"
                       ng-click="$event.stopPropagation()"
                       style="pointer-events: none;">
            </div>
        </div>
    </div>
</div>

<!-- List Mode -->
<div ng-if="::field.config.viewMode === 'list'" class="image-vote-list">
    <div ng-repeat="option in ::field.fieldOptions track by $index" 
         class="image-vote-list-item"
         ng-class="{'selected': isImageVoteOptionSelected(field, option)}"
         ng-click="toggleImageVoteOption(field, option)">
        
        <div class="image-vote-list-item__selector">
            <input type="{{::field.config.selectionMode === 'multiple' ? 'checkbox' : 'radio'}}" 
                   ng-checked="isImageVoteOptionSelected(field, option)"
                   ng-click="$event.stopPropagation()"
                   style="pointer-events: none;">
        </div>
        
        <div class="image-vote-list-item__image" ng-if="option.imageUrl">
            <img ng-src="{{::option.imageUrl}}" 
                 alt="{{::option.label}}"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23ddd\' width=\'80\' height=\'80\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'10\'%3ESem imagem%3C/text%3E%3C/svg%3E'">
        </div>
        
        <div class="image-vote-list-item__content">
            <div class="image-vote-list-item__title">{{::option.label}}</div>
            <div ng-if="::option.description" class="image-vote-list-item__description">
                {{::option.description}}
            </div>
        </div>
    </div>
</div>

<!-- Image Only Mode -->
<div ng-if="::field.config.viewMode === 'image-only'" 
     class="image-vote-image-only"
     ng-class="'image-size-' + (field.config.imageSize || 'medium')">
    
    <div ng-repeat="option in ::field.fieldOptions track by $index" 
         class="image-vote-image-item"
         ng-class="{'selected': isImageVoteOptionSelected(field, option)}"
         ng-click="toggleImageVoteOption(field, option)"
         title="{{::option.label}}{{::option.description ? ' - ' + option.description : ''}}">
        
        <div class="image-vote-image-item__wrapper" ng-if="option.imageUrl">
            <img ng-src="{{::option.imageUrl}}" 
                 alt="{{::option.label}}"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3E{{option.label}}%3C/text%3E%3C/svg%3E'">
        </div>
        
        <div ng-if="isImageVoteOptionSelected(field, option)" class="image-vote-image-item__check">
            <span class="icon icon-check"></span>
        </div>
    </div>
</div>

<script>
(function() {
    var scope = angular.element(document).scope();
    
    if (!scope.isImageVoteOptionSelected) {
        scope.isImageVoteOptionSelected = function(field, option) {
            if (!option || !option.id) return false;
            
            var fieldName = 'field_' + field.id;
            var value = scope.data.entity[fieldName];
            
            if (field.config.selectionMode === 'multiple') {
                return Array.isArray(value) && value.indexOf(option.id) > -1;
            } else {
                return value === option.id;
            }
        };

        scope.toggleImageVoteOption = function(field, option) {
            if (!option || !option.id) return;
            
            var fieldName = 'field_' + field.id;
            var value = scope.data.entity[fieldName];
            
            if (field.config.selectionMode === 'multiple') {
                // Seleção múltipla
                if (!Array.isArray(value)) {
                    value = [];
                }
                
                var index = value.indexOf(option.id);
                if (index > -1) {
                    // Desmarcar
                    value.splice(index, 1);
                } else {
                    // Verificar limite máximo
                    var maxOptions = parseInt(field.config.maxOptions) || 0;
                    if (maxOptions > 0 && value.length >= maxOptions) {
                        alert('Você já atingiu o limite máximo de ' + maxOptions + ' opções.');
                        return;
                    }
                    // Marcar
                    value.push(option.id);
                }
                
                scope.data.entity[fieldName] = value;
            } else {
                // Seleção única
                if (value === option.id) {
                    // Desmarcar se clicar na mesma opção
                    scope.data.entity[fieldName] = null;
                } else {
                    // Marcar nova opção
                    scope.data.entity[fieldName] = option.id;
                }
            }
            
            scope.saveField(field, scope.data.entity[fieldName]);
            scope.$apply();
        };
    }
})();
</script>
