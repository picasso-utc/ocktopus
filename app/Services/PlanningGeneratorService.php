<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PlanningGeneratorService
{
    private array $planningTemplates = [
        'permLundi' => [
            '18:30-19:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:00-19:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:30-20:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '20:00-20:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '20:30-21:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '21:00-21:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '21:30-22:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '22:00-22:30' => ['Sécu Trottoir' => 3, 'Ménage' => 'rest'],
            '22:30-23:00' => ['Ménage' => 'all'],
        ],
        'permMardi' => [
            '18:30-19:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:00-19:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:30-20:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '20:00-20:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '20:30-21:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '21:00-21:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '21:30-22:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '22:00-22:30' => ['Sécu Trottoir' => 3, 'Ménage' => 'rest'],
            '22:30-23:00' => ['Ménage' => 'all'],
        ],
        'permMercredi' => [
            '18:30-19:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:00-19:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:30-20:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '20:00-20:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '20:30-21:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 5],
            '21:00-21:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 5],
            '21:30-22:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '22:00-22:30' => ['Sécu Trottoir' => 4, 'Ménage' => 'rest'],
            '22:30-23:00' => ['Ménage' => 'all'],
        ],
        'permJeudi' => [
            '18:30-19:00' => ['Bar' => 4, 'Caisse' => 1, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '19:00-19:30' => ['Bar' => 4, 'Caisse' => 1, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '19:30-20:00' => ['Bar' => 5, 'Caisse' => 2, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '20:00-20:30' => ['Bar' => 5, 'Caisse' => 2, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '20:30-21:00' => ['Bar' => 6, 'Caisse' => 2, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '21:00-21:30' => ['Bar' => 6, 'Caisse' => 2, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '21:30-22:00' => ['Bar' => 6, 'Caisse' => 2, 'Sécu Pente' => 2, 'Sécu Escalier' => 2],
            '22:00-22:30' => ['Sécu Trottoir' => 5, 'Ménage' => 'rest'],
            '22:30-23:00' => ['Ménage' => 'all'],
        ],
        'permVendredi' => [
            '18:30-19:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:00-19:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 3],
            '19:30-20:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '20:00-20:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 1, 'Bar' => 4],
            '20:30-21:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 5],
            '21:00-21:30' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 5],
            '21:30-22:00' => ['Sécu Pente' => 2, 'Sécu Escalier' => 2, 'Caisse' => 2, 'Bar' => 4],
            '22:00-22:30' => ['Sécu Trottoir' => 4, 'Ménage' => 'rest'],
            '22:30-23:00' => ['Ménage' => 'all'],
        ]
    ];

    public function generate(string $jour, array $participants, array $modifications): array
    {
        if (!isset($this->planningTemplates[$jour])) {
            throw new \InvalidArgumentException("Jour invalide: $jour");
        }
        $this->perms = $this->planningTemplates[$jour];  // Récup le planning correspondant
    
        foreach ($modifications as $modif) {     // Applique les modifications d'effectifs ou de perms
            $horaire = $modif['horaire'];
            $permanence = $modif['permanence'];
            $nombre = (int) $modif['nombre'];
    
            if (isset($this->perms[$horaire][$permanence])) {
                $this->perms[$horaire][$permanence] = $nombre;
            } else {
                $this->perms[$horaire][$modif['autre_permanence']] = $nombre;
            }
        }
    
        return $this->assignParticipants($participants);
    }

    private function assignParticipants(array $participants): array
    {
        $planning = [];
        $availableParticipants = array_keys($participants);
        $taskCounts = array_fill_keys($availableParticipants, 0); // Tab pour equilibrer le nb perm
        $horaireKeys = array_keys($this->perms);
    
        // Attribution des permanences générales de 18h30 à 22h00
        foreach ($horaireKeys as $index => $horaire) {
            if ($horaire >= '22:00') break;
            $planning[$horaire] = [];
    
            foreach ($this->perms[$horaire] as $task => $count) {
                $assigned = 0;
    
                // On garde que les participants disponibles pour une perm à cet horaire
                $sortedParticipants = array_filter($availableParticipants, function ($p) use ($participants, $horaire) {
                    list($permStart, $permEnd) = explode('-', $horaire);
                    $permStart = new \DateTime($permStart);
                    $permEnd = new \DateTime($permEnd);
                    return new \DateTime($participants[$p]['debut']) <= $permStart &&
                           new \DateTime($participants[$p]['fin']) >= $permEnd;
                });
    
                // Tri par nb taches effectuées
                usort($sortedParticipants, function($a, $b) use ($taskCounts) {
                    return $taskCounts[$a] <=> $taskCounts[$b];
                });
    
                foreach ($sortedParticipants as $selectedParticipant) {
                    $name = $participants[$selectedParticipant]['nom'];
                    
                    $previousHoraire = $horaireKeys[$index - 1] ?? null;
                    if (
                        ($task === 'Sécu Pente' || $task === 'Sécu Escalier') &&  // Si c'est une sécu
                        $previousHoraire &&  // Qu'il y a déjà un des perms
                        isset($planning[$previousHoraire][$name]) && 
                        in_array($planning[$previousHoraire][$name], ['Sécu Pente', 'Sécu Escalier']) // Et que la personne a déjà fait une sécu avant
                    ) {
                        continue; // Ca saute
                    }
    
                    // On attribue les perms
                    if (!isset($planning[$horaire][$name])) {  
                        $planning[$horaire][$name] = $task;
                        $taskCounts[$selectedParticipant]++;
                        $assigned++;
                        if ($assigned >= $count) break;
                    }
                }
            }
        }

        // Attribution des Sécu Pente et Sécu Escalier de 22h à 22h30
        if (isset($planning['21:30-22:00'])) {
            foreach ($planning['21:30-22:00'] as $participant => $task) {
                if (in_array($task, ['Sécu Pente', 'Sécu Escalier']) && !isset($planning['22:00-22:30'][$participant])) {
                    $planning['22:00-22:30'][$participant] = $task;
                }
            }
        }

        // Attribution des Sécu Trottoir
        if (isset($this->perms['22:00-22:30']['Sécu Trottoir'])) {
            shuffle($availableParticipants);
            $assigned = 0;
            foreach ($availableParticipants as $selectedParticipant) {
                
                $participantDebut = new \DateTime($participants[$selectedParticipant]['debut']); // Idem on vérifie que les participants sont dispo
                $participantFin = new \DateTime($participants[$selectedParticipant]['fin']);
                $permStart = new \DateTime('22:00');
                $permEnd = new \DateTime('22:30');
                if ($participantDebut > $permStart || $participantFin < $permEnd) {
                    continue;
                }

                if (!isset($planning['22:00-22:30'][$participants[$selectedParticipant]['nom']])) {
                    $planning['22:00-22:30'][$participants[$selectedParticipant]['nom']] = 'Sécu Trottoir';
                    $assigned++;
                    if ($assigned >= $this->perms['22:00-22:30']['Sécu Trottoir']) break;
                }
            }
        }

        // Attribution du Ménage (22:00 - 22:30)
        if (isset($this->perms['22:00-22:30']['Ménage'])) {
            shuffle($availableParticipants);
            foreach ($availableParticipants as $selectedParticipant) {

                $participantDebut = new \DateTime($participants[$selectedParticipant]['debut']);
                $participantFin = new \DateTime($participants[$selectedParticipant]['fin']);
                $permStart = new \DateTime('22:00');
                $permEnd = new \DateTime('22:30');
                if ($participantDebut > $permStart || $participantFin < $permEnd) {
                    continue;
                }

                if (!isset($planning['22:00-22:30'][$participants[$selectedParticipant]['nom']])) {
                    $planning['22:00-22:30'][$participants[$selectedParticipant]['nom']] = 'Ménage';
                }
            }
        }

        // Attribution du Ménage (22:30 - 23:00)
        if (isset($this->perms['22:30-23:00']['Ménage'])) {
            shuffle($availableParticipants);
            foreach ($availableParticipants as $selectedParticipant) {
                
                $participantDebut = new \DateTime($participants[$selectedParticipant]['debut']);
                $participantFin = new \DateTime($participants[$selectedParticipant]['fin']);
                $permStart = new \DateTime('22:30');
                $permEnd = new \DateTime('23:00');
                if ($participantDebut > $permStart || $participantFin < $permEnd) {
                    continue;
                }

                $planning['22:30-23:00'][$participants[$selectedParticipant]['nom']] = 'Ménage';
            }
        }

        return $planning;
    }
}