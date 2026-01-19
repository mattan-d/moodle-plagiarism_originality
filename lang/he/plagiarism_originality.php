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
 * plagiarism_originality HE
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'דרוג מקוריות - גילוי העתקות';
$string['originality'] = 'רכיב דרוג מקוריות מסמכים - גילוי העתקות';
$string['originality_help'] =
        'התקן לגילוי העתקות בעבודות מלל (טקסט). אין להשתמש עבור עבודות בשפה אחרת או בעבודות הנדסיות שונות כי המנגנון לא בנוי לכך.';
$string['originality_shortname'] = 'מקוריות';
$string['plugin_server_type'] = 'שרת מקוריות';
$string['plugin_settings'] = 'מאפיינים של מקוריות';
$string['plugin_enabled'] = 'הפעלת הרכיב';
$string['plugin_connected'] = 'מזהה מפתח תקין, מחובר למערכת מקוריות';
$string['student_disclosure'] =
        "עליך לסמן √ במקום המתאים עבור שליחת המטלה לבדיקת מקוריות. ללא סימון זה לא יהיה ניתן להגיש עבודה זו.<br>הגשה זו היא מקורית, שייכת לי, נערכה בידיי ובהגשתי זו אני לוקח/ת אחריות על מקוריות הכתוב בתוכה.<br><br>למעט המקומות שבהם ציינתי שהעבודה נעשתה עי אחרים וקישור מתאים נמצא בביבליוגרפיה או במקום הדרוש לכך.<br><br>אני מודע/ת ומסכים/ה שמטלה זו תיבדק לגילוי גניבה ספרותית על ידי חברת מקוריות ואני מסכים/ה <a rel='external' href='https://www.originality.co.il/termsOfUse.html' target='_blank' style='text-decoration:underline'>לתנאי  השימוש</a>.";
$string['secret'] = 'מפתח שימוש';
$string['key'] = 'מפתח שימוש';
$string['key_help'] = 'על מנת להשתמש ברכיב עליך להיות בעל מפתח שימוש';
$string['saved_failed'] = 'מפתח השימוש שהוקש שגוי, הרכיב אינו פעיל';
$string['checking_inprocessmsg'] = 'בבדיקה';
$string['checking_unprocessable'] = 'לא ניתן לעיבוד';
$string['submitted_before_activation'] = 'הוגש לפני הפעלת הרכיב';
$string['service_is_inactive'] = 'רכיב מקוריות אינו פעיל. יש לפנות אל אחראי מוודל.';
$string['warning_message'] = "יש לסמן את כפתור ההסכמה ('אני מודע ומסכים') על מנת להפעיל את כפתור השליחה";
$string['previous_submissions'] = 'קיימות עבודות שהוגשו כבר. על הסטודנטים האלה להגיש שנית על מנת שמקוריות עבודתם תיבדק.';
$string['production_endpoint'] =
        '<b>שרת מקוריות</b>&nbsp;&nbsp;<span style="font-size:14px;">לשלוח את העבודות לשרת היצור של מקוריות.</span>';
$string['test_endpoint'] =
        '<b>שרת ניסוי</b>&nbsp;&nbsp;<span style="font-size:14px;">לשלוח את העבודות לשרת הניסוי (טסט) של מקוריות. יש לבחור זאת רק לאחר תיאום עם מקוריות.</span>';
$string['check_ghostwriter'] = 'רכיב גילוי כותב צללים לעבודות גדולות';
$string['check_ghostwriter_help'] = 'ניתן להפעיל רכיב זה רק לאחר תיאום עם חברת מקוריות. ללא תיאום מראש, הרכיב לא יפעל.';
$string['check_ghostwriter_label'] = 'כותב צללים';
$string['ghostwriter_enabled'] = 'הפעל בדיקת כותב צללים';
$string['ghostwriter_failed_message'] = 'לא ניתן לבצע בדיקת צללים עבור מלל מקוון';
$string['pdf:filename'] = 'צפיה בדוח מקוריות';
$string['originality_unsupported_file'] = 'קובץ אינו נתמך.';
$string['default_settings_assignments'] = 'הפעל בדיקה במטלות חדשות';
$string['document_submitted'] = 'מקוריות API';
$string['document_submitted_desc'] = 'טקסט או תוכן הקובץ נשלח לשרת מקוריות.';
$string['privacy:metadata:aicc:data'] = 'נתונים אישיים שהועברו מתת המערכת.';
$string['privacy:metadata:aicc:externalpurpose'] = 'עותק פיזי של תוכן טקסט או קובץ שנשלח למקוריות';
$string['privacy:metadata:plagiarism_originality_sub'] = 'נתונים אישיים מטבלת המשנה';
$string['privacy:metadata:plagiarism_originality_sub:userid'] = 'נתוני משתמש אישי מטבלת המשנה';
$string['originality:manage'] = 'ניהול אסימון שירות אינטרנט';
$string['merge_reports'] = 'הסבה של דוחות מקוריות';
$string['stuck_submissions'] = 'שליחת עבודות תקועות';
