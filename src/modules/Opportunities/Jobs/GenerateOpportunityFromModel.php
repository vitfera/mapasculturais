<?php
namespace Opportunities\Jobs;

use MapasCulturais\App;
use MapasCulturais\Definitions\JobType;
use MapasCulturais\Entities\Job;
use MapasCulturais\Entities\Opportunity;

class GenerateOpportunityFromModel extends JobType
{
    public const SLUG = 'GenerateOpportunityFromModel';

    protected function _generateId(array $data, string $start_string, string $interval_string, int $iterations)
    {
        return self::SLUG . ":{$data['targetOpportunity']->id}";
    }

    protected function _execute(Job $job)
    {
        $app = App::i();

        /** @var Opportunity|null $source */
        $source = $job->sourceOpportunity;
        /** @var Opportunity|null $target */
        $target = $job->targetOpportunity;
        $authenticatedUser = $job->authenticatedUser ?: $job->user;

        if (!$source || !$target) {
            $app->log->error('[GenerateOpportunityFromModelJob] Job sem oportunidade de origem ou destino.');
            return true;
        }

        if ($target->getMetadata('modelGenerationStatus') === 'ready') {
            return true;
        }

        $target->setMetadata('modelGenerationStatus', 'processing');
        $target->setMetadata('modelGenerationError', null);
        $target->save(true);

        try {
            $app->conn->beginTransaction();
            $app->disableAccessControl();

            try {
                $controller = $app->controller('opportunity');
                $target = $controller->processOpportunityGenerationFromModel($source, $target, [
                    'objectType' => $job->objectType,
                    'ownerEntity' => $job->ownerEntity,
                ]);
            } finally {
                $app->enableAccessControl();
            }

            $app->conn->commit();

            $target = $target->refreshed();
            $target->setMetadata('modelGenerationStatus', 'ready');
            $target->setMetadata('modelGenerationError', null);
            $target->save(true);

            try {
                $this->sendSuccessMailNotification($authenticatedUser, $target);
            } catch (\Throwable $mailError) {
                $app->log->error('[GenerateOpportunityFromModelJob] Erro ao enviar e-mail de sucesso: ' . $mailError->getMessage());
            }
        } catch (\Throwable $error) {
            if ($app->conn->isTransactionActive()) {
                $app->conn->rollBack();
            }

            $app->log->error(sprintf(
                '[GenerateOpportunityFromModelJob] Erro ao gerar oportunidade #%d a partir do modelo #%d: %s',
                $target->id,
                $source->id,
                $error->getMessage()
            ));
            $app->log->error($error->getTraceAsString());

            $app->em->clear();
            $target = $app->repo('Opportunity')->find($target->id);
            if ($target) {
                $target->setMetadata('modelGenerationStatus', 'failed');
                $target->setMetadata('modelGenerationError', mb_substr($error->getMessage(), 0, 1000));
                $target->save(true);
            }

            $this->sendErrorMailNotification($authenticatedUser, $source, $target);

            return false;
        }

        return true;
    }

    private function sendSuccessMailNotification($user, Opportunity $opportunity): void
    {
        if (!$user) {
            return;
        }

        $app = App::i();
        $message = $app->renderMailerTemplate('generate_opportunity_from_model_success', [
            'userName' => $user->profile->name,
            'opportunityTitle' => $opportunity->name,
            'opportunityUrl' => $opportunity->editUrl,
        ]);

        $app->createAndSendMailMessage([
            'from' => $app->config['mailer.from'],
            'to' => $user->email,
            'subject' => sprintf("[{$app->siteName}] %s", $message['title']),
            'body' => $message['body'],
        ]);
    }

    private function sendErrorMailNotification($user, Opportunity $source, ?Opportunity $target): void
    {
        if (!$user) {
            return;
        }

        try {
            $app = App::i();
            $message = $app->renderMailerTemplate('generate_opportunity_from_model_error', [
                'userName' => $user->profile->name,
                'modelTitle' => $source->name,
                'opportunityTitle' => $target?->name,
            ]);

            $app->createAndSendMailMessage([
                'from' => $app->config['mailer.from'],
                'to' => $user->email,
                'subject' => sprintf("[{$app->siteName}] %s", $message['title']),
                'body' => $message['body'],
            ]);
        } catch (\Throwable $mailError) {
            App::i()->log->error('[GenerateOpportunityFromModelJob] Erro ao enviar e-mail de falha: ' . $mailError->getMessage());
        }
    }
}
