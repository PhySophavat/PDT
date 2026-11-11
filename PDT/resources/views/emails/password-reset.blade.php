<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>លេខកូដ OTP កំណត់ពាក្យសម្ងាត់ថ្មី - {{ $schoolName }}</title>
  <style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body {
      margin: 0 !important;
      padding: 0 !important;
      background-color: #f1f5f9;
      font-family: 'Kantumruy Pro', 'Hanuman', 'Siemreap', Arial, sans-serif;
      color: #334155;
      line-height: 1.6;
    }
  </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 24px 12px;">
    <tr>
      <td align="center">
        <!-- Main Email Container -->
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
          
          <!-- Top Header Bar -->
          <tr>
            <td align="center" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); padding: 36px 24px; text-align: center;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <div style="display: inline-block; width: 64px; height: 64px; background-color: #ffffff; border-radius: 16px; padding: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);">
                      <img src="{{ asset('images/logo.png') }}" alt="Logo" width="52" height="52" style="display: block; margin: auto; object-fit: contain;">
                    </div>
                    <h1 style="margin: 14px 0 4px 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;">
                      portal sala
                    </h1>
                    <p style="margin: 0; color: #93c5fd; font-size: 13px; font-weight: 500;">
                      {{ $schoolName }} • លេខកូដសម្ងាត់ OTP សម្រាប់កំណត់ពាក្យសម្ងាត់ថ្មី
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Main Content Body -->
          <tr>
            <td style="padding: 32px 28px;">
              
              <!-- Greeting & Status Badge -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td>
                    <div style="display: inline-block; background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 9999px; margin-bottom: 16px;">
                      🛡️ លេខកូដផ្ទៀងផ្ទាត់ OTP (One-Time Password)
                    </div>
                    <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 18px; font-weight: 700;">
                      សួស្តី {{ $recipientName }}!
                    </h2>
                    <p style="margin: 0 0 16px 0; font-size: 13px; color: #475569; line-height: 1.7;">
                      យើងបានទទួលសំណើសុំផ្លាស់ប្តូរពាក្យសម្ងាត់សម្រាប់គណនីរបស់អ្នកនៅក្នុងប្រព័ន្ធ <strong>portal sala</strong>។ សូមយកលេខកូដ OTP ៦ ខ្ទង់ ខាងក្រោមនេះ ទៅវាយបញ្ចូលក្នុងទំព័រផ្ទៀងផ្ទាត់៖
                    </p>
                  </td>
                </tr>
              </table>

              <!-- 6-Digit OTP Box -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%); border: 2px dashed #3b82f6; border-radius: 16px; padding: 22px 16px; margin: 20px 0; text-align: center;">
                <tr>
                  <td align="center">
                    <p style="margin: 0 0 8px 0; font-size: 11px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 1px;">
                      លេខកូដ OTP របស់អ្នកគឺ៖
                    </p>
                    <div style="display: inline-block; background-color: #ffffff; border: 2px solid #2563eb; border-radius: 14px; padding: 12px 32px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15); margin: 6px 0;">
                      <span style="font-family: 'Courier New', Courier, monospace; font-size: 34px; font-weight: 900; color: #1d4ed8; letter-spacing: 8px;">
                        {{ $otpCode }}
                      </span>
                    </div>
                    <p style="margin: 8px 0 0 0; font-size: 12px; color: #64748b; font-weight: 500;">
                      ⏳ លេខកូដនេះមានសុពលភាពត្រឹមតែ <strong>{{ $expireMinutes }} នាទី</strong> ប៉ុណ្ណោះ
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Action Button CTA -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; text-align: center;">
                <tr>
                  <td align="center">
                    <a href="{{ $resetUrl }}" style="display: inline-block; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #ffffff; font-size: 13px; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 12px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); letter-spacing: 0.3px;">
                      🔒 ឬចុចត្រង់នេះដើម្បីកំណត់ពាក្យសម្ងាត់ផ្ទាល់
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Security Warning Box -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 14px; padding: 16px; margin: 20px 0;">
                <tr>
                  <td>
                    <div style="font-size: 12px; font-weight: 700; color: #b45309; margin-bottom: 6px;">
                      ⚠️ បម្រាមសុវត្ថិភាពសំខាន់៖
                    </div>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #92400e; line-height: 1.6;">
                      <li>លេខកូដ OTP នេះអាចវាយខុសបានអតិបរមាត្រឹមតែ <strong>៥ ដង</strong> ប៉ុណ្ណោះ។ បើខុសលើសពីនេះ កូដនឹងត្រូវលុបចោលជាបន្ទាន់។</li>
                      <li>លេខកូដនេះអាចប្រើប្រាស់បាន <strong>តែម្តងគត់ (Single-Use)</strong>។</li>
                      <li>កុំចែករំលែកលេខកូដនេះទៅកាន់បុគ្គលណាផ្សេងឡើយ បុគ្គលិកសាលានឹងមិនសុំលេខកូដ OTP របស់អ្នកជាដាច់ខាត។</li>
                    </ul>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer Bar -->
          <tr>
            <td align="center" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 20px; text-align: center;">
              <p style="margin: 0 0 4px 0; font-size: 12px; color: #64748b; font-weight: 600;">
                © {{ date('Y') }} portal sala • {{ $schoolName }}
              </p>
              <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                សារនេះផ្ញើចេញដោយស្វ័យប្រវត្តពីប្រព័ន្ធគ្រប់គ្រងសាលារៀន ជំនាន់ថ្មី។
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
