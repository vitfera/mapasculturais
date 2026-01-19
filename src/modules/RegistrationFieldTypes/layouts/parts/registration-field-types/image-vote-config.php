<?php use MapasCulturais\i; ?>
<div ng-if="field.fieldType === 'image-vote'">
    <div class="mc-content">
        <label>
            <?php i::_e('Modo de seleção') ?><br>
            <select ng-model="field.config.selectionMode">
                <option value="single"><?php i::_e('Seleção única (escolher apenas uma opção)') ?></option>
                <option value="multiple"><?php i::_e('Seleção múltipla (escolher várias opções)') ?></option>
            </select>
        </label>

        <label ng-if="field.config.selectionMode === 'multiple'">
            <?php i::_e('Limite de Opções') ?><br>
            <input type="number" ng-model="field.config.maxOptions">
            <small class="registration-help"><?php i::_e('Digite o limite de opções. Deixe em branco ou 0 para ilimitado.'); ?></small>
        </label>

        <label>
            <?php i::_e('Modo de visualização') ?><br>
            <select ng-model="field.config.viewMode">
                <option value="grid"><?php i::_e('Grade de imagens com título') ?></option>
                <option value="list"><?php i::_e('Lista com miniaturas') ?></option>
                <option value="image-only"><?php i::_e('Apenas imagens (título no hover)') ?></option>
            </select>
        </label>

        <label>
            <?php i::_e('Tamanho das imagens') ?><br>
            <select ng-model="field.config.imageSize">
                <option value="small"><?php i::_e('Pequeno') ?></option>
                <option value="medium"><?php i::_e('Médio') ?></option>
                <option value="large"><?php i::_e('Grande') ?></option>
            </select>
        </label>

        <h4><?php i::_e('Opções de votação') ?></h4>
        <small class="registration-help"><?php i::_e('Configure as opções que estarão disponíveis para votação'); ?></small>
        
        <div ng-if="!field.fieldOptions || field.fieldOptions.length === 0">
            <p><em><?php i::_e('Nenhuma opção adicionada ainda. Clique no botão abaixo para adicionar.'); ?></em></p>
        </div>
        
        <div ng-repeat="option in field.fieldOptions track by $index" style="border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 4px; background: #f9f9f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <label>
                        <strong><?php i::_e('Título da opção') ?>:</strong><br>
                        <input type="text" 
                               ng-model="option.label" 
                               placeholder="<?php i::esc_attr_e('Ex: Opção 1'); ?>" 
                               required 
                               style="width: 100%; max-width: 400px; padding: 8px; margin-top: 5px;">
                    </label>
                    
                    <label style="margin-top: 15px; display: block;">
                        <strong><?php i::_e('Valor da opção') ?>:</strong><br>
                        <input type="text" 
                               ng-model="option.id" 
                               placeholder="<?php i::esc_attr_e('Ex: opcao1 (será gerado automaticamente se deixar em branco)'); ?>" 
                               style="width: 100%; max-width: 400px; padding: 8px; margin-top: 5px;">
                        <small class="registration-help"><?php i::_e('Identificador único da opção. Deixe em branco para gerar automaticamente.'); ?></small>
                    </label>
                    
                    <label style="margin-top: 15px; display: block;">
                        <strong><?php i::_e('Imagem da opção') ?>:</strong><br>
                        
                        <div ng-if="option.imageUrl" style="margin: 10px 0;">
                            <img ng-src="{{option.imageUrl}}" 
                                 alt="{{option.label}}"
                                 style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px; display: block; margin-bottom: 10px;">
                            <button type="button" 
                                    ng-click="removeImageVoteImage(field, $index)" 
                                    class="btn btn-danger btn-small">
                                <span class="icon icon-trash"></span> <?php i::_e('Remover imagem') ?>
                            </button>
                        </div>
                        
                        <div ng-if="!option.imageUrl" style="margin-top: 10px;">
                            <a class="btn btn-default edit hltip" 
                               hltitle="<?php i::esc_attr_e('Adicionar imagem'); ?>" 
                               href="#"
                               ng-click="openImageVoteUpload(field, $index, $event)">
                                <span class="icon icon-image"></span> <?php i::_e('Adicionar imagem') ?>
                            </a>
                            
                            <edit-box id="editbox-image-vote-option-{{$index}}" 
                                     position="right" 
                                     title="<?php i::esc_attr_e('Adicionar imagem'); ?>"
                                     cancel-label="<?php i::esc_attr_e('Cancelar'); ?>"
                                     submit-label="<?php i::esc_attr_e('Enviar'); ?>"
                                     on-submit="sendImageVoteFile"
                                     close-on-cancel="true"
                                     spinner-condition="data.uploadSpinner">
                                <form class="js-ajax-upload" 
                                      method="post"
                                      action="<?php echo $app->createUrl('registrationfieldconfiguration', 'upload'); ?>"
                                      data-group="image_vote_field"
                                      enctype="multipart/form-data">
                                    <div class="alert danger hidden"></div>
                                    <p class="form-help"><?php i::_e("Tamanho máximo do arquivo: ");?><?php echo $app->view->maxUploadSizeFormatted; ?></p>
                                    <input type="file" name="image_vote_file" accept="image/*" />
                                    <div class="js-ajax-upload-progress">
                                        <div class="progress inactive">
                                            <div class="bar"></div>
                                            <div class="percent">0%</div>
                                        </div>
                                    </div>
                                </form>
                            </edit-box>
                        </div>
                    </label>

                    <label style="margin-top: 15px; display: block;">
                        <strong><?php i::_e('Descrição (opcional)') ?>:</strong><br>
                        <textarea ng-model="option.description" 
                                  placeholder="<?php i::esc_attr_e('Descrição da opção'); ?>" 
                                  style="width: 100%; max-width: 400px; min-height: 60px; padding: 8px; margin-top: 5px;"></textarea>
                    </label>
                </div>

                <button type="button" 
                        ng-click="removeImageVoteOption(field, $index)" 
                        class="btn btn-danger btn-small" 
                        style="margin-left: 15px;">
                    <span class="icon icon-trash"></span> <?php i::_e('Remover') ?>
                </button>
            </div>
        </div>

        <button type="button" 
                ng-click="addImageVoteOption(field)" 
                class="btn btn-primary" 
                style="margin-top: 15px;">
            <span class="icon icon-add"></span> <?php i::_e('Adicionar opção') ?>
        </button>
    </div>
</div>

<script>
(function() {
    // Aguardar o scope do AngularJS estar disponível
    angular.element(document).ready(function() {
        var $injector = angular.element(document.body).injector();
        if (!$injector) return;
        
        var $rootScope = $injector.get('$rootScope');
        var $timeout = $injector.get('$timeout');
        
        // Interceptar a função processFieldConfiguration para não converter array do image-vote
        $timeout(function() {
            // Procurar pelo scope correto
            var scope = angular.element('[ng-controller="RegistrationConfigurationsController"]').scope();
            if (scope && scope.data && scope.data.fields) {
                // Adicionar watch para manter fieldOptions como array para image-vote
                scope.$watch('data.fields', function(fields) {
                    if (fields) {
                        fields.forEach(function(field) {
                            if (field.fieldType === 'image-vote' && typeof field.fieldOptions === 'string') {
                                try {
                                    field.fieldOptions = JSON.parse(field.fieldOptions);
                                } catch(e) {
                                    field.fieldOptions = [];
                                }
                            }
                        });
                    }
                }, true);
                
                // Adicionar funções diretamente no scope do controller
                scope.addImageVoteOption = function(field) {
                    if (!field.fieldOptions) {
                        field.fieldOptions = [];
                    }
                    
                    // Gerar ID automático
                    var newId = 'opcao_' + (field.fieldOptions.length + 1);
                    
                    field.fieldOptions.push({
                        id: newId,
                        label: '',
                        imageUrl: '',
                        description: ''
                    });
                };
                
                scope.removeImageVoteOption = function(field, index) {
                    if (confirm('<?php i::_e('Deseja remover esta opção?'); ?>')) {
                        field.fieldOptions.splice(index, 1);
                    }
                };
                
                scope.removeImageVoteImage = function(field, index) {
                    if (confirm('<?php i::_e('Deseja remover esta imagem?'); ?>')) {
                        field.fieldOptions[index].imageUrl = '';
                    }
                };
                
                // Função para enviar arquivo
                scope.sendImageVoteFile = function(attrs) {
                    var editboxId = attrs.id;
                    var $form = $('#' + editboxId + ' form');
                    $form.submit();
                };
                
                // Função para inicializar o uploader quando o editbox abrir
                scope.openImageVoteUpload = function(field, optionIndex, event) {
                    if (event) {
                        event.preventDefault();
                    }
                    
                    var editboxId = 'editbox-image-vote-option-' + optionIndex;
                    
                    // Abrir o editbox
                    scope.editbox.open(editboxId, event);
                    
                    // Inicializar o uploader
                    setTimeout(function() {
                        var $form = $('#' + editboxId + ' form');
                        
                        if ($form.data('initialized')) {
                            return;
                        }
                        
                        MapasCulturais.AjaxUploader.init($form);
                        $form.data('initialized', true);
                        
                        // Evento de cancelamento
                        $('#' + editboxId).on('cancel', function(){
                            if($form.data('xhr')) {
                                $form.data('xhr').abort();
                            }
                            $form.get(0).reset();
                            MapasCulturais.AjaxUploader.resetProgressBar($form.parent());
                        });
                        
                        // Adicionar evento de sucesso customizado
                        $form.on('ajaxForm.success', function(evt, response) {
                            var group = $form.data('group');
                            
                            if (response[group] && response[group].url) {
                                scope.$apply(function() {
                                    field.fieldOptions[optionIndex].imageUrl = response[group].url;
                                });
                                
                                // Fechar o editbox
                                setTimeout(function(){
                                    scope.editbox.close(editboxId, event);
                                }, 700);
                                
                                MapasCulturais.Messages.success('<?php i::_e('Imagem enviada com sucesso!'); ?>');
                            } else {
                                MapasCulturais.Messages.error('<?php i::_e('Erro ao processar resposta do upload.'); ?>');
                            }
                            
                            // Reset do formulário
                            $form.get(0).reset();
                            MapasCulturais.AjaxUploader.resetProgressBar($form.parent());
                        });
                    }, 100);
                };
            }
        }, 500);
    });
})();
</script>
