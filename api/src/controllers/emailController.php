<?php

use Model\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class emailController
{
  private Email $emailModel;
  private $middleware;

  public function __construct()
  {
    $this->emailModel = new Email();
    $this->middleware = new Middleware\ApiKeyMiddleware();
  }

  public function handleRequest(): void
  {
    try {
      $this->middleware->handle();
    } catch (Exception $e) {
      $this->sendResponse(401, [
        "error" => "Unauthorized: " . $e->getMessage(),
      ]);
      return;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    if (!isset($input["recipient"], $input["subject"])) {
      $this->sendResponse(400, [
        "error" => "Invalid input. Required: recipient, subject.",
      ]);
      return;
    }

    $recipientEmail = $input["recipient"];
    $subject = $input["subject"] ?? "Default Subject";
    $senderEmail = $input["sender_email"] ?? "default-email@example.com";
    $senderName = $input["sender_name"] ?? "Default Sender";
    $body = $input["body"] ?? null;
    $templateId = $input["template_id"] ?? null;

    try {
      if (!$this->emailModel->ValidateEmailFromRequest($recipientEmail)) {
        $this->sendResponse(404, [
          "error" => "The email address is not registered.",
        ]);
        return;
      }
      $emailLimit = $this->middleware->isUnderDailyEmailLimit();
      if (!$emailLimit) {
        $this->sendResponse(429, [
          "error" => "You Can No Longer Recive Emails Today",
        ]);
        return;
      }
      $userName =
        $this->emailModel->GetUserNameFromRequest($recipientEmail) ??
        "Valued User";

      if (!$body) {
        $templates = self::getTemplates();
        $body = $templates[$templateId] ?? $templates[1];
        $body = str_replace("{{username}}", htmlspecialchars($userName), $body);
        $body = str_replace(
          "{{sender_name}}",
          htmlspecialchars($senderName),
          $body
        );
        $body = str_replace(
          "{{sender_email}}",
          htmlspecialchars($senderEmail),
          $body
        );
      } else {
        $body = str_replace("{{username}}", htmlspecialchars($userName), $body);
      }

      $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host = $_ENV["MAIL_HOST"];
      $mail->SMTPAuth = true;
      $mail->Username = $_ENV["MAIL_USERNAME"];
      $mail->Password = $_ENV["MAIL_PASSWORD"];
      $mail->SMTPSecure = $_ENV["MAIL_SMTPSECURE"];
      $mail->Port = $_ENV["MAIL_PORT"];
      $mail->setFrom($senderEmail, $senderName);
      $mail->addReplyTo($senderEmail, $senderName);
      $mail->addAddress($recipientEmail);
      $mail->Subject = $subject;
      $mail->isHTML(true);
      $mail->Body = $body;

      if ($mail->send()) {
        $this->sendResponse(200, ["message" => "Email sent successfully."]);
        $this->middleware->incrementEmailCount();
      } else {
        throw new Exception("Failed to send the email.");
      }
    } catch (Exception $e) {
      error_log("Error sending email: " . $e->getMessage());
      $this->sendResponse(500, ["error" => "An unexpected error occurred."]);
    }
  }

  private function sendResponse(int $statusCode, array $data): void
  {
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode($data);
  }

  public static function getTemplates(): array
  {
    return [
      1 => "
    <html>
      <body style='font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0;'>
        <div style='background-color: #f4f4f9; padding: 40px;'>
          <div style='background-color: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e0e0e0; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333333;'>Hello, {{username}},</h2>
            <p style='color: #555555; font-size: 20px;'>Someone has reached out to you through your portfolio. Check out the details below and get back to them as soon as you can!</p>
            <div style='margin-top: 20px;'>
              <p style='color: #555555;'><strong>Sender Name:</strong> {{sender_name}}</p>
              <p style='color: #555555;'><strong>Sender Email:</strong> {{sender_email}}</p>
            </div>
            <div style='margin-top: 30px; text-align: center;'>
              <a href='mailto:{{sender_email}}' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 4px;'>Reply to {{sender_name}}</a>
            </div>
            <p style='margin-top: 20px; color: #777777; font-size: 14px;'>If you didn’t expect a message, feel free to ignore this notification. Stay awesome!</p>
            <div style='margin-top: 40px; text-align: center; font-size: 15px; color: #777777;'>
              <p style='font-style: italic;'>From your friendly web mailer. Powered by <span style='color: #007bff; font-weight: bold;'>PortfolioApi</span>. Building connections one email at a time!</p>
            </div>
          </div>
        </div>
      </body>
    </html>",

      2 => "
    <html>
      <body style='font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0;'>
        <div style='background-color: #fff; padding: 50px 20px;'>
          <div style='background-color: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e0e0e0; max-width: 600px; margin: 0 auto;'>
            <h1 style='color: #333333;'>New Portfolio Message</h1>
            <p style='color: #555555; font-size: 17px;'>Dear {{username}},</p>
            <p style='color: #555555; font-size: 20px;'>We wanted to inform you that someone has reached out to you via your portfolio. Here’s the message they left for you:</p>
            <div style='margin-top: 20px;'>
              <p style='color: #555555;'><strong>Sender Name:</strong> {{sender_name}}</p>
              <p style='color: #555555;'><strong>Email:</strong> {{sender_email}}</p>
            </div>
            <div style='margin-top: 30px; text-align: center;'>
              <a href='mailto:{{sender_email}}' style='background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 4px;'>Reply Now</a>
            </div>
            <p style='margin-top: 20px; color: #777777; font-size: 14px;'>This is an automated message from your portfolio. Please respond if you wish to continue the conversation.</p>
            <div style='margin-top: 40px; text-align: center; font-size: 15px; color: #777777;'>
              <p style='font-style: italic;'>Sent with love from your portfolio. Powered by <span style='color: #28a745; font-weight: bold;'>PortfolioApi</span>. Helping you stay connected!</p>
            </div>
          </div>
        </div>
      </body>
    </html>",

      3 => "
    <html>
      <body style='font-family: 'Arial', sans-serif; margin: 0; padding: 0;'>
        <div style='background-color: #f7f7f7; padding: 60px 20px;'>
          <div style='background-color: #ffffff; padding: 35px; border-radius: 8px; border: 1px solid #ddd; max-width: 650px; margin: 0 auto;'>
            <h2 style='color: #333333;'>Hello {{username}},</h2>
            <p style='color: #555555; font-size: 20px;'>Someone is trying to reach you through your portfolio. Check out the details and respond when you can!</p>
            <div style='margin-top: 25px;'>
              <p style='color: #555555;'><strong>Sender Name:</strong> {{sender_name}}</p>
              <p style='color: #555555;'><strong>Email:</strong> {{sender_email}}</p>
            </div>
            <div style='margin-top: 35px; text-align: center;'>
              <a href='mailto:{{sender_email}}' style='background-color: #007bff; color: white; padding: 12px 28px; text-decoration: none; font-weight: bold; border-radius: 5px;'>Reply to {{sender_name}}</a>
            </div>
            <p style='margin-top: 30px; color: #888888; font-size: 14px;'>This is an automated notification from your portfolio. Please feel free to ignore it if you were not expecting a message.</p>
            <div style='margin-top: 40px; text-align: center; font-size:15px; color: #888888;'>
              <p style='font-style: italic;'>From your portfolio’s mailer. Powered by <span style='color: #007bff; font-weight: bold;'>PortfolioApi</span>. Connecting professionals with opportunities!</p>
            </div>
          </div>
        </div>
      </body>
    </html>",

      4 => "
    <html>
      <body style='font-family: 'Arial', sans-serif; margin: 0; padding: 0;'>
        <div style='background-color: #121212; padding: 50px 20px;'>
          <div style='background-color: #1a1a1a; padding: 40px; border-radius: 10px; border: 1px solid #333333; max-width: 600px; margin: 0 auto; color: white;'>
            <h1 style='color: #ff9800;'>New Portfolio Message</h1>
            <p style='color: #cccccc;'>Hey {{username}},</p>
            <p style='color: #cccccc; font-size: 20px;'>Someone has just reached out to you via your portfolio. Check the details and get back to them as soon as you can!</p>
            <div style='margin-top: 20px;'>
              <p style='color: #cccccc;'><strong>Sender Name:</strong> {{sender_name}}</p>
              <p style='color: #cccccc;'><strong>Email:</strong> {{sender_email}}</p>
            </div>
            <div style='margin-top: 30px; text-align: center;'>
              <a href='mailto:{{sender_email}}' style='background-color: #ff9800; color: white; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 4px;'>Reply Now</a>
            </div>
            <p style='margin-top: 20px; color: #888888; font-size: 14px;'>This is an automated message sent from your portfolio system. Ignore if not needed.</p>
            <div style='margin-top: 40px; text-align: center; font-size: 15px; color: #888888;'>
              <p style='font-style: italic;'>Brought to you by your Portfolio’s mailer. Powered by <span style='color: #ff9800; font-weight: bold;'>PortfolioApi</span>. Let’s stay connected!</p>
            </div>
          </div>
        </div>
      </body>
    </html>",

      5 => "
    <html>
      <body style='font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0;'>
        <div style='background-color: #1e1e1e; padding: 50px 20px;'>
          <div style='background-color: #333333; padding: 40px; border-radius: 8px; border: 1px solid #555555; max-width: 600px; margin: 0 auto; color: white;'>
            <h2 style='color: #00bcd4;'>You Have a New Message!</h2>
            <p style='color: #cccccc;'>Hello {{username}},</p>
            <p style='color: #cccccc;font-size: 20px;'>We wanted to let you know that someone reached out to you through your portfolio. Here are the details:</p>
            <div style='margin-top: 20px;'>
              <p style='color: #cccccc;'><strong>Sender Name:</strong> {{sender_name}}</p>
              <p style='color: #cccccc;'><strong>Email:</strong> {{sender_email}}</p>
            </div>
            <div style='margin-top: 30px; text-align: center;'>
              <a href='mailto:{{sender_email}}' style='background-color: #00bcd4; color: white; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 4px;'>Reply Now</a>
            </div>
            <p style='margin-top: 20px; color: #888888; font-size: 14px;'>If you didn’t expect this message, feel free to disregard it.</p>
            <div style='margin-top: 40px; text-align: center; font-size: 15px; color: #888888;'>
              <p style='font-style: italic;'>Powered by <span style='color: #00bcd4; font-weight: bold;'>PortfolioApi</span>. Connecting creative minds!</p>
            </div>
          </div>
        </div>
      </body>
    </html>",
    ];
  }
}
