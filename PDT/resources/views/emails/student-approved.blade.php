<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ការអនុម័តចុះឈ្មោះចូលរៀន - សាលារៀន ជំនាន់ថ្មី PDT</title>
  <style>
    /* Client resets */
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
            <td align="center" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%); padding: 36px 24px; text-align: center;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <div style="display: inline-block; width: 64px; height: 64px; background-color: #ffffff; border-radius: 16px; padding: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                      <img src="{{ asset('images/logo.png') }}" alt="PDT School Logo" width="52" height="52" style="display: block; margin: auto; object-contain: contain;">
                    </div>
                    <h1 style="margin: 14px 0 4px 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;">
                      សាលារៀន ជំនាន់ថ្មី PDT
                    </h1>
                    <p style="margin: 0; color: #dbeafe; font-size: 13px; font-weight: 500;">
                      PDT New Generation School • ប្រព័ន្ធគ្រប់គ្រងសាលារៀន
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
                    <div style="display: inline-block; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px;">
                      ✓ បានអនុម័តការចុះឈ្មោះជោគជ័យ
                    </div>
                    <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 18px; font-weight: 700;">
                      សួស្តីប្អូនសិស្សានុសិស្ស {{ $student->name_khmer ?: $student->name_latin }}!
                    </h2>
                    <p style="margin: 0 0 18px 0; font-size: 13px; color: #475569; line-height: 1.7;">
                      សាលារៀនមានសេចក្តីសោមនស្សរីករាយសូមជម្រាបជូនថា ពាក្យស្នើសុំចុះឈ្មោះចូលរៀនរបស់ប្អូនសម្រាប់ <strong>ថ្នាក់ {{ $student->grade }}</strong> ត្រូវបានគណៈគ្រប់គ្រងសាលាត្រួតពិនិត្យ និង <strong>អនុម័តជាផ្លូវការ</strong> រួចរាល់ហើយ។
                    </p>
                    <p style="margin: 0 0 24px 0; font-size: 13px; color: #475569; line-height: 1.7;">
                      ខាងក្រោមនេះគឺជាព័ត៌មានគណនីសម្គាល់ខ្លួន និងទិន្នន័យសម្រាប់ចូលប្រើប្រាស់ប្រព័ន្ធគ្រប់គ្រងការសិក្សា (sala-portal) របស់សាលា៖
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Credentials Box -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                <tr>
                  <td>
                    <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 14px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px;">
                      🔑 ព័ត៌មានគណនីចូលប្រព័ន្ធ (Login Credentials)
                    </div>

                    <!-- Student ID -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 10px;">
                      <tr>
                        <td width="38%" style="font-size: 12px; color: #64748b; font-weight: 600;">អត្តលេខសិស្ស (ID)៖</td>
                        <td width="62%" style="font-size: 13px; color: #0f172a; font-weight: 700; font-family: monospace;">
                          {{ $student->student_id_card }}
                        </td>
                      </tr>
                    </table>

                    <!-- Login Email -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 10px;">
                      <tr>
                        <td width="38%" style="font-size: 12px; color: #64748b; font-weight: 600;">អ៊ីមែលចូលប្រព័ន្ធ៖</td>
                        <td width="62%" style="font-size: 13px; color: #2563eb; font-weight: 700; word-break: break-all;">
                          {{ $student->email }}
                        </td>
                      </tr>
                    </table>

                    <!-- Assigned Grade -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 10px;">
                      <tr>
                        <td width="38%" style="font-size: 12px; color: #64748b; font-weight: 600;">ថ្នាក់រៀនផ្លូវការ៖</td>
                        <td width="62%" style="font-size: 13px; color: #0f172a; font-weight: 700;">
                          ថ្នាក់ {{ $student->grade }}
                        </td>
                      </tr>
                    </table>

                    <!-- Temporary Password -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; margin-top: 10px;">
                      <tr>
                        <td width="42%" style="font-size: 12px; color: #991b1b; font-weight: 700;">ពាក្យសម្ងាត់បណ្តោះអាសន្ន៖</td>
                        <td width="58%" style="font-size: 16px; color: #dc2626; font-weight: 800; font-family: monospace; letter-spacing: 1px;">
                          {{ $plainPassword }}
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>
              </table>

              <!-- Action Button -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 26px;">
                <tr>
                  <td align="center">
                    <a href="{{ $loginUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 34px; border-radius: 9999px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); text-align: center;">
                      🚀 ចូលប្រើប្រព័ន្ធភ្លាមៗ (Login Now) &rarr;
                    </a>
                    <p style="margin: 9px 0 0 0; font-size: 11px; color: #64748b; text-align: center;">
                      ✨ ចុចប៊ូតុងខាងលើដើម្បីចូលប្រព័ន្ធដោយស្វ័យប្រវត្តិ (1-Click Direct Login) ដោយមិនបាច់វាយពាក្យសម្ងាត់ឡើយ
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Security Warning Alert Box -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
                <tr>
                  <td width="28" valign="top" style="font-size: 18px; color: #d97706; padding-right: 8px;">⚠️</td>
                  <td style="font-size: 12px; color: #92400e; line-height: 1.6;">
                    <strong>ការណែនាំអំពីសុវត្ថិភាព៖</strong> ដើម្បីសុវត្ថិភាពផ្ទាល់ខ្លួន សូមប្អូនសិស្សានុសិស្សធ្វើការផ្លាស់ប្តូរពាក្យសម្ងាត់ផ្ទាល់ខ្លួនភ្លាមៗ បន្ទាប់ពីបានចូលប្រព័ន្ធជាលើកដំបូង។ សូមកុំចែករំលែកពាក្យសម្ងាត់នេះទៅកាន់អ្នកដទៃ។
                  </td>
                </tr>
              </table>

              <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6;">
                ប្រសិនបើប្អូនមានចម្ងល់ ឬជួបបញ្ហាក្នុងការចូលប្រើប្រាស់ សូមទាក់ទងមកកាន់ការិយាល័យរដ្ឋបាលសាលារៀន ឬតាមរយៈទូរស័ព្ទជំនួយការសាលា។
              </p>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 24px; text-align: center;">
              <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; color: #334155;">
                សាលារៀន ជំនាន់ថ្មី PDT (PDT New Generation School)
              </p>
              <p style="margin: 0 0 10px 0; font-size: 11px; color: #64748b;">
                បណ្តុះបណ្តាលចំណេះដឹង បច្ចេកវិទ្យា និងនវានុវត្តន៍អប់រំជំនាន់ថ្មី
              </p>
              <p style="margin: 0; font-size: 10px; color: #94a3b8;">
                &copy; {{ date('Y') }} សាលារៀន ជំនាន់ថ្មី PDT. រក្សាសិទ្ធិគ្រប់យ៉ាង។
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>

