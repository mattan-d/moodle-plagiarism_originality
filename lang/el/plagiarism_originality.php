<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism_originality EL.
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Αυθεντικότητα - Ανίχνευση Πλαγιοκατασκευής';
$string['originality'] = 'Αυθεντικότητα - Ανίχνευση Πλαγιοκατασκευής Εγγράφων';
$string['originality_help'] =
        'Ενεργοποιήστε την ανίχνευση πλαγιοκατασκευής για εργασίες βασισμένες σε κείμενο. Μην το χρησιμοποιείτε για εργασίες σε διάφορες γλώσσες ή για μηχανολογικές εργασίες, καθώς το μηχανισμός δεν έχει σχεδιαστεί για αυτό.';
$string['originality_shortname'] = 'Αυθεντικότητα';
$string['plugin_server_type'] = 'Διακομιστής Αυθεντικότητας';
$string['plugin_settings'] = 'Ρυθμίσεις Αυθεντικότητας';
$string['plugin_enabled'] = 'Ενεργοποίηση του προσθέτου';
$string['plugin_connected'] = 'Έγκυρο κλειδί API, συνδεδεμένο με το σύστημα Αυθεντικότητας';
$string['student_disclosure'] =
        "Πρέπει να επισημάνετε το √ στην αντίστοιχη θέση για να υποβάλετε την εργασία για έλεγχο αυθεντικότητας. Χωρίς αυτή την επισήμανση, δεν θα είναι δυνατή η υποβολή αυτής της εργασίας.<br>Αυτή η υποβολή είναι αυθεντική, ανήκει σε εμένα, διαμορφώθηκε από εμένα και φέρω την ευθύνη για την αυθεντικότητα του περιεχομένου.<br><br>Εκτός από τις περιπτώσεις όπου αναφέρθηκε ότι η εργασία δημιουργήθηκε από άλλους και υπάρχει σχετικός σύνδεσμος στη βιβλιογραφία ή στην απαιτούμενη θέση.<br><br>Είμαι ενήμερος/η και συμφωνώ ότι αυτή η εργασία θα ελεγχθεί για ανίχνευση πνευματικής κλοπής από την εταιρεία Originality και αποδέχομαι τους <a rel='external' href='https://originality.world/termsOfUseGR.html' target='_blank' style='text-decoration:underline'>Όρους Χρήσης</a>.";
$string['secret'] = 'Κλειδί Πρόσβασης';
$string['key'] = 'Κλειδί Πρόσβασης';
$string['key_help'] = 'Για να χρησιμοποιήσετε αυτό το πρόσθετο, χρειάζεστε ένα κλειδί πρόσβασης.';
$string['saved_failed'] = 'Εισαγωγή μη έγκυρου κλειδιού πρόσβασης, το πρόσθετο δεν είναι ενεργό.';
$string['checking_inprocessmsg'] = 'Σε εξέλιξη';
$string['checking_unprocessable'] = 'Αδυναμία επεξεργασίας';
$string['submitted_before_activation'] = 'Υποβλήθηκε πριν την ενεργοποίηση του προσθέτου';
$string['service_is_inactive'] =
        'Το πρόσθετο Αυθεντικότητας δεν είναι ενεργό. Παρακαλώ επικοινωνήστε με τον διαχειριστή του Moodle.';
$string['warning_message'] =
        "Πρέπει να επιλέξετε το πλαίσιο συναίνεσης ('Είμαι ενημερωμένος και συμφωνώ') για να ενεργοποιήσετε το κουμπί υποβολής.";
$string['previous_submissions'] =
        'Υπάρχουν ήδη υποβληθείσες εργασίες. Αυτοί οι φοιτητές πρέπει να υποβάλουν ξανά την εργασία τους για να γίνει ο έλεγχος αυθεντικότητας.';
$string['production_endpoint'] =
        '<b>Παραγωγικός Διακομιστής</b>&nbsp;&nbsp;<span style="font-size:14px;">Υποβολή εργασιών στον παραγωγικό διακομιστή της Αυθεντικότητας.</span>';
$string['test_endpoint'] =
        '<b>Δοκιμαστικός Διακομιστής</b>&nbsp;&nbsp;<span style="font-size:14px;">Υποβολή εργασιών στον δοκιμαστικό διακομιστή της Αυθεντικότητας. Επιλέξτε αυτήν την επιλογή μόνο μετά από συνεννόηση με την Αυθεντικότητα.</span>';
$string['check_ghostwriter'] = 'Έλεγχος Σκιώδους Συγγραφέα για Μεγάλες Εργασίες';
$string['check_ghostwriter_help'] =
        'Μπορείτε να ενεργοποιήσετε αυτό το πρόσθετο μόνο μετά από συνεννόηση με την Αυθεντικότητα. Χωρίς προηγούμενη συνεννόηση, το πρόσθετο δεν θα λειτουργήσει.';
$string['check_ghostwriter_label'] = 'Σκιώδης Συγγραφέας';
$string['ghostwriter_enabled'] = 'Ενεργοποίηση έλεγχου Σκιώδους Συγγραφέα';
$string['ghostwriter_failed_message'] = 'Δεν είναι δυνατή η εκτέλεση ελέγχου Σκιώδους Συγγραφέα για διαδικτυακό κείμενο';
$string['pdf:filename'] = 'Προβολή αναφοράς αυθεντικότητας';
$string['default_settings_assignments'] = 'Ενεργοποίηση ελέγχου για νέες εργασίες';
$string['merge_reports'] = 'Συγχώνευση παλαιών αρχείων αναφορών σε νέα δομή';
$string['stuck_submissions'] = 'Προσπάθεια επανακομιδής υποβολών που έχουν κολλήσει';
