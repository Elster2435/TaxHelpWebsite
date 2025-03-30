<?php
require 'vendor/autoload.php';
use function morphos\russian\inflectName;
use morphos\Russian\Cases;
use PhpOffice\PhpWord\TemplateProcessor;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Yekaterinburg');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payerName = htmlspecialchars($_POST['payer_name']);
    $inflected = inflectName($payerName, Cases::GENITIVE);
    $receiptMethod = htmlspecialchars($_POST['receipt_method']);
    if ($receiptMethod == 'Электронный вид') {
        $receiptText = "Прошу направить справку в ИФНС образовательной организации для дальнейшей её загрузки инспекцией в Личный кабинет налогоплательщика.";
        $receiptTextEmail = "Вы выбрали получение справки в электронном виде — она будет направлена в ИФНС через образовательную организацию. Справка будет готова в течение 5 рабочих дней.";
    } else if ($receiptMethod == 'Бумажный вид') {
        $receiptText = "Хочу забрать справку лично в бухгалтерии по адресу г. Пермь, б-р Гагарина, 57, каб. 106.";
        $receiptTextEmail = "Вы выбрали получение справки лично — её можно будет забрать в бухгалтерии по адресу: г. Пермь, б-р Гагарина 57, каб. 106. Справка будет готова в течение 5 рабочих дней.";
    }
    $data = [
        'payer_name' => htmlspecialchars($_POST['payer_name']),
        'payer_name_genitive' => $inflected,
        'payer_birthdate' => date("d.m.Y", strtotime($_POST['payer_birthdate'])),
        'payer_inn' => htmlspecialchars($_POST['payer_inn']),
        'payer_passport' => htmlspecialchars($_POST['payer_passport']),
        'payer_passport_date' => date("d.m.Y", strtotime($_POST['payer_passport_date'])),
        'payer_contact' => htmlspecialchars($_POST['payer_contact']),
        'payer_payment_year' => htmlspecialchars($_POST['payer_payment_year']),
        'student_name' => htmlspecialchars($_POST['student_name']),
        'student_birthdate' => date("d.m.Y", strtotime($_POST['student_birthdate'])),
        'student_inn' => htmlspecialchars($_POST['student_inn']),
        'student_passport' => htmlspecialchars($_POST['student_passport']),
        'student_passport_date' => date("d.m.Y", strtotime($_POST['student_passport_date'])),
        'email' => htmlspecialchars($_POST['email']),
	    'submission_date' => date("d.m.Y"),
        'receipt_method_text' => $receiptText
    ];

    $templateProcessor = new TemplateProcessor('Document.docx');
    foreach ($data as $key => $value) {
        $templateProcessor->setValue($key, $value);
    }

    $filename = "Заявление от $inflected.docx";
    $filepath = __DIR__ . "/uploads/$filename";
    $templateProcessor->saveAs($filepath);

    if (isset($_POST['download_file'])) {
        header("Content-Description: File Transfer");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
        unlink($filepath);
        exit;
    }

    if (isset($_POST['send_email']) && !empty($_POST['email'])) {
        $userEmail = htmlspecialchars($_POST['email']);
        $staticEmail = '-'; // Почта учебного заведения
        $mail = new PHPMailer(true);
        $mail->CharSet = $mail::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.yandex.ru';
        $mail->SMTPAuth = true;
        $mail->Username = '-'; // Почта учебного заведения
        $mail->Password = '-'; // Ключ доступа к почте (не пароль)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('-', '-'); // Почта учебного заведения и имя отправителя (например, Учебное заведение им.Пупкина)
        $mail->addAddress($staticEmail);
        $mail->Subject = "Заявление на налоговый вычет от {$inflected}";
        $mail->Body = "{$payerName} отправил заявление на налоговый вычет. Смотреть вложение.";
        $mail->addAttachment($filepath);
        $mail->send();

        $mail = new PHPMailer(true);
        $mail->CharSet = $mail::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.yandex.ru';
        $mail->SMTPAuth = true;
        $mail->Username = '-'; // Почта учебного заведения
        $mail->Password = '-'; // Ключ доступа к почте (не пароль)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('-', '-'); // Почта учебного заведения и имя отправителя (например, Учебное заведение им.Пупкина)
        $mail->addAddress($userEmail);
        $mail->Subject = "Заявление на налоговый вычет";
        $mail->Body = "Здравствуйте!\n\nВаше заявление сформировано и прикреплено к этому письму.\n\n{$receiptTextEmail}";
        $mail->addAttachment($filepath);
        $mail->send();

        unlink($filepath);
    }
    echo "<script>
            alert('Файл успешно отправлен на почту!');
            window.location.href = 'index.php';
          </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ справки</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function copyPayerData() {
            document.getElementById('student_name').value = document.getElementById('payer_name').value;
            document.getElementById('student_birthdate').value = document.getElementById('payer_birthdate').value;
            document.getElementById('student_inn').value = document.getElementById('payer_inn').value;
            document.getElementById('student_passport').value = document.getElementById('payer_passport').value;
            document.getElementById('student_passport_date').value = document.getElementById('payer_passport_date').value;
        }
    </script>
</head>
<body>
    <header>
        <div class="container header-content">
            <img src="rue_logo.svg" alt="Логотип">
            <h1>Заявление на выдачу справки об оплате образовательных услуг</h1>
        </div>
    </header>

    <main class="container">
        <section class="form-section">
            <form method="POST" enctype="multipart/form-data">
                <h2>Данные плательщика</h2>

                <div class="form-group">
                    <label for="payer_name">ФИО плательщика:</label>
                    <input type="text" id="payer_name" name="payer_name" required>
                </div>

                <div class="form-group">
                    <label for="payer_birthdate">Дата рождения плательщика:</label>
                    <input type="date" id="payer_birthdate" name="payer_birthdate" required>
                </div>

                <div class="form-group">
                    <label for="payer_inn">ИНН плательщика:</label>
                    <input type="text" id="payer_inn" name="payer_inn" required>
                </div>

                <div class="form-group">
                    <label for="payer_passport">Серия и номер паспорта:</label>
                    <input type="text" id="payer_passport" name="payer_passport" required>
                </div>

                <div class="form-group">
                    <label for="payer_passport_date">Дата выдачи паспорта:</label>
                    <input type="date" id="payer_passport_date" name="payer_passport_date" required>
                </div>

                <div class="form-group">
                    <label for="payer_contact">Контактная информация (телефон):</label>
                    <input type="text" id="payer_contact" name="payer_contact" required>
                </div>

                <div class="form-group">
                    <label for="payer_payment_year">Год оплаты образовательных услуг:</label>
                    <input type="number" id="payer_payment_year" name="payer_payment_year" value="2024" min="2024" required>
                </div>

                <button type="button" onclick="copyPayerData()" class="btn copy-btn">Скопировать из плательщика</button>

                <h2>Данные студента</h2>

                <div class="form-group">
                    <label for="student_name">ФИО студента:</label>
                    <input type="text" id="student_name" name="student_name" required>
                </div>

                <div class="form-group">
                    <label for="student_birthdate">Дата рождения студента:</label>
                    <input type="date" id="student_birthdate" name="student_birthdate" required>
                </div>

                <div class="form-group">
                    <label for="student_inn">ИНН студента:</label>
                    <input type="text" id="student_inn" name="student_inn" required>
                </div>

                <div class="form-group">
                    <label for="student_passport">Серия и номер паспорта студента:</label>
                    <input type="text" id="student_passport" name="student_passport" required>
                </div>

                <div class="form-group">
                    <label for="student_passport_date">Дата выдачи паспорта студента:</label>
                    <input type="date" id="student_passport_date" name="student_passport_date" required>
                </div>

                <h2>Способ получения справки</h2>

                <div class="form-group form-radio-group">
                    <label class="radio-option">
                        <input type="radio" name="receipt_method" value="Электронный вид" required>
                        <span>Направить справку в ИФНС в электронном виде</span>
                    </label>
                    <div class="hint-text" id="hint-electronic">В случае выбора данного варианта справка будет направлена в ИФНС образовательной организации для дальнейшей
                        ее загрузки иснпекцией в "Личный кабинет налогоплательщика".</div>
                    <label class="radio-option">
                        <input type="radio" name="receipt_method" value="Бумажный вид" required>
                        <span>Получить лично в бумажном виде</span>
                    </label>
                    <div class="hint-text" id="hint-paper">В случае выбора данного варианта выдача справки будет производиться лично заявителю в бухгалтерии по адресу
                        г. Пермь, б-р Гагарина 57, каб. 106, с 10:00 до 17:00. Обед с 13:00 до 14:00. Справка будет готова в течение 5 рабочих дней.</div>
                </div>

                <div class="form-group">
                    <label for="email">Ваш e-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <input type="submit" id="submit_button" name="send_email" value="Отправить на почту" class="btn">
            </form>
        </section>
    </main>
    <script>
    function showHint(hintId) {
        document.querySelectorAll('.hint-text').forEach(el => {
            el.style.display = 'none';
        });
        const hint = document.getElementById(hintId);
        if (hint) {
            hint.style.display = 'block';
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="receipt_method"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'Электронный вид') {
                    showHint('hint-electronic');
                } else if (this.value === 'Бумажный вид') {
                    showHint('hint-paper');
                }
            });
        });
    });
    </script>
</body>
</html>
