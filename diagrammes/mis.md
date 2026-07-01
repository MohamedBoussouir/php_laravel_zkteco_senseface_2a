ntégration et communication avec les terminaux ZKTeco (Protocole ADMS) — DeviceController (handshake, receiveData, getRequests, deviceCmd)
CRUD pour la gestion des utilisateurs et des appareils — EmployeeController + DashboardController
Récupération et synchronisation des données de pointage (Logs) — AttendanceController::fetch, DeviceController::receiveData
Interface d'authentification et gestion des rôles (Admin/Employé) — AuthController (guard auth:admin)
Génération des rapports de présence/retard (Export PDF/Excel) — ReportController + AttendanceController
Gestion avancée des horaires (Shifts) et plannings de travail — ScheduleController
Tests unitaires et validation — tests/Unit, tests/Feature
Déploiement (Docker) — docker-compose.yml
Gestion des données biométriques — Enrôlement/suppression empreintes, upload photo/visage, reconnaissance faciale (EmployeeController)
Enrôlement à distance des empreintes sur l'appareil — EmployeeController::enrollFingerprint (commande ENROLL_FP)
Synchronisation bidirectionnelle des employés avec les appareils — fetchFromDevice + syncUserToDevice
Gestion des commandes à distance (DeviceCommand) — File d'attente de commandes exécutées par l'appareil
Gestion des Work Patterns (hebdomadaires) — 7 jours avec shift assigné
Gestion des Monthly Patterns (mensuels) — 31 jours avec shift assigné
Génération automatique du rotationnaire (Roster) — generateRoster basé sur le pattern
Assignation des patterns aux employés — updateWorkPattern, assignPattern, assignMonthlyPattern
Configuration des paramètres système — Synchronisation heure appareil, mode logging (device/calculated), wallpaper
Configuration SIP — SipController (page d'info IP)
Support multilingue — Arabe, Français, Anglais (SetLocale middleware)
Nettoyage automatique des anciens payloads — Prunable (supprime les payloads > 7 jours)
Suppression logique (SoftDeletes) et restauration des employés — restore, forceDelete
Export des pointages et rapports en PDF/Excel — 4 routes d'export
API temps réel — Route /api/latest-attendance pour le dernier pointage en JSON
Dashboard avec statistiques — DashboardController::index (compteurs appareils, employés, présences/absences)