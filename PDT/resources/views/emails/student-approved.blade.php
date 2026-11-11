<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ការអនុម័តពាក្យស្នើសុំចុះឈ្មោះចូលរៀនជោគជ័យ - សាលារៀន ជំនាន់ថ្មី PDT</title>
  <style>
    /* Client resets */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body {
      margin: 0 !important;
      padding: 0 !important;
      background-color: #f1f5f9;
      font-family: 'Kantumruy Pro', 'Hanuman', 'Siemreap', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #334155;
      line-height: 1.6;
    }
    @media only screen and (max-width: 600px) {
      .email-container { width: 100% !important; border-radius: 0 !important; }
      .email-padding { padding: 24px 18px !important; }
      .header-padding { padding: 28px 18px !important; }
      .credential-table td { display: block !important; width: 100% !important; }
      .credential-label { padding-bottom: 2px !important; }
      .credential-val { padding-bottom: 12px !important; }
      .action-btn { width: 100% !important; box-sizing: border-box !important; padding: 14px 20px !important; font-size: 15px !important; }
    }
  </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 24px 10px;">
    <tr>
      <td align="center">
        <!-- Main Email Container (Max 600px) -->
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08), 0 4px 10px -3px rgba(15, 23, 42, 0.04); border: 1px solid #e2e8f0;">
          
          <!-- Modern Branding Header -->
          <tr>
            <td align="center" class="header-padding" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%); padding: 36px 24px; text-align: center;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <!-- School Logo Card -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" style="background-color: #ffffff; border-radius: 18px; padding: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.2);">
                          <img src="{{ asset('images/logo.png') }}" alt="PDT Logo" width="56" height="56" style="display: block; margin: 0 auto; object-fit: contain;">
                        </td>
                      </tr>
                    </table>
                    
                    <h1 style="margin: 16px 0 4px 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.3px;">
                      portal sala • សាលារៀន ជំនាន់ថ្មី
                    </h1>
                    <p style="margin: 0; color: #bfdbfe; font-size: 13px; font-weight: 500;">
                      PDT New Generation School Management System
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Main Content Body -->
          <tr>
            <td class="email-padding" style="padding: 36px 32px;">
              
              <!-- Success Pill Badge -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 18px;">
                <tr>
                  <td style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 9999px;">
                    ✓ បានអនុម័តការចុះឈ្មោះជាផ្លូវការ
                  </td>
                </tr>
              </table>

              <!-- Greeting & Welcome Message -->
              <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 20px; font-weight: 800;">
                សូមស្វាគមន៍ប្អូន {{ $student->name_khmer ?: $student->name_latin }}! 👋
              </h2>
              <p style="margin: 0 0 16px 0; font-size: 14px; color: #475569; line-height: 1.75;">
                សាលារៀនមានសេចក្តីសោមនស្សរីករាយសូមជម្រាបជូនថា ពាក្យស្នើសុំចុះឈ្មោះចូលរៀនរបស់ប្អូនសម្រាប់ <strong style="color: #1e40af;">ថ្នាក់ {{ $student->grade }}</strong> ត្រូវបានគណៈគ្រប់គ្រងសាលាត្រួតពិនិត្យ និង <strong>អនុម័តជាផ្លូវការរួចរាល់ហើយ</strong>។
              </p>
              <p style="margin: 0 0 24px 0; font-size: 14px; color: #475569; line-height: 1.75;">
                គណនីសម្គាល់ខ្លួនរបស់ប្អូនត្រូវបានបង្កើតឡើងក្នុងប្រព័ន្ធ។ ខាងក្រោមនេះជាព័ត៌មានគណនីសម្រាប់ចូលប្រើប្រាស់ប្រព័ន្ធគ្រប់គ្រងការសិក្សា (portal-sala)៖
              </p>

              <!-- Credentials Box Card -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 22px; margin-bottom: 26px;">
                <tr>
                  <td>
                    <!-- Card Header -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-bottom: 1px dashed #cbd5e1; padding-bottom: 12px; margin-bottom: 14px;">
                      <tr>
                        <td>
                          <span style="font-size: 14px; font-weight: 800; color: #0f172a;">
                            🔑 ព័ត៌មានគណនីចូលប្រព័ន្ធ (Login Credentials)
                          </span>
                        </td>
                      </tr>
                    </table>

                    <!-- Table of Credentials -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="credential-table">
                      <!-- Student ID -->
                      <tr>
                        <td width="40%" class="credential-label" style="font-size: 13px; color: #64748b; font-weight: 600; padding: 6px 0;">
                          អត្តលេខសិស្ស (ID)៖
                        </td>
                        <td width="60%" class="credential-val" style="font-size: 14px; color: #0f172a; font-weight: 800; font-family: 'SF Mono', Monaco, Consolas, monospace; letter-spacing: 0.5px; padding: 6px 0;">
                          <span style="background-color: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; border: 1px solid #dbeafe;">{{ $student->student_id_card }}</span>
                        </td>
                      </tr>

                      <!-- Email -->
                      <tr>
                        <td class="credential-label" style="font-size: 13px; color: #64748b; font-weight: 600; padding: 6px 0;">
                          អ៊ីមែលចូលប្រព័ន្ធ៖
                        </td>
                        <td class="credential-val" style="font-size: 13px; color: #2563eb; font-weight: 700; word-break: break-all; padding: 6px 0;">
                          {{ $student->email }}
                        </td>
                      </tr>

                      <!-- Grade -->
                      <tr>
                        <td class="credential-label" style="font-size: 13px; color: #64748b; font-weight: 600; padding: 6px 0;">
                          ថ្នាក់រៀនផ្លូវការ៖
                        </td>
                        <td class="credential-val" style="font-size: 14px; color: #0f172a; font-weight: 700; padding: 6px 0;">
                          ថ្នាក់ {{ $student->grade }}
                        </td>
                      </tr>

                      <!-- Temporary Password -->
                      <tr>
                        <td colspan="2" style="padding-top: 10px;">
                          <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 12px 16px;">
                            <tr>
                              <td width="42%" style="font-size: 12px; color: #9f1239; font-weight: 700;">
                                ពាក្យសម្ងាត់បណ្តោះអាសន្ន៖
                              </td>
                              <td width="58%" align="right" style="font-size: 17px; color: #e11d48; font-weight: 900; font-family: 'SF Mono', Monaco, Consolas, monospace; letter-spacing: 1.5px; user-select: all; -webkit-user-select: all;">
                                {{ $plainPassword }}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>
              </table>

              <!-- Big Primary CTA: 1-Click Auto Login -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
                <tr>
                  <td align="center">
                    <a href="{{ $loginUrl }}" target="_blank" class="action-btn" style="display: inline-block; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%); color: #ffffff !important; text-decoration: none; font-size: 15px; font-weight: 800; padding: 16px 40px; border-radius: 9999px; box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.4); text-align: center; letter-spacing: 0.2px;">
                      🚀 ចូលទៅកាន់ប្រព័ន្ធសាលារៀន (Login Now) &rarr;
                    </a>
                    
                    <p style="margin: 12px 0 0 0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6;">
                      ✨ <strong>ងាយស្រួលបំផុត៖</strong> ចុចប៊ូតុងខាងលើដើម្បីចូលប្រព័ន្ធដោយស្វ័យប្រវត្តិ (1-Click Direct Login) ដោយមិនចាំបាច់វាយពាក្យសម្ងាត់ឡើយ!
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Direct URL Fallback (in case button click is blocked) -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-radius: 12px; padding: 12px 16px; margin-bottom: 24px;">
                <tr>
                  <td style="font-size: 11px; color: #64748b; line-height: 1.6; word-break: break-all;">
                    🔗 <strong>ប្រសិនបើចុចប៊ូតុងខាងលើមិនដំណើរការ</strong> សូមចម្លងតំណភ្ជាប់នេះទៅបើកក្នុង Web Browser ដោយផ្ទាល់៖<br>
                    <a href="{{ $loginUrl }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">{{ $loginUrl }}</a>
                  </td>
                </tr>
              </table>

              <!-- 3-Step Quick Start Guide -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 18px 20px; margin-bottom: 22px;">
                <tr>
                  <td>
                    <div style="font-size: 13px; font-weight: 800; color: #166534; margin-bottom: 10px;">
                      🎯 ៣ ជំហានងាយៗក្នុងការចាប់ផ្តើម៖
                    </div>
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; color: #15803d; line-height: 1.7;">
                      <tr>
                        <td width="24" valign="top" style="font-weight: 800;">១.</td>
                        <td style="padding-bottom: 6px;">ចុចប៊ូតុង <strong>«ចូលទៅកាន់ប្រព័ន្ធសាលារៀន»</strong> ខាងលើដើម្បី Login ចូលប្រព័ន្ធភ្លាមៗ។</td>
                      </tr>
                      <tr>
                        <td width="24" valign="top" style="font-weight: 800;">២.</td>
                        <td style="padding-bottom: 6px;">ពិនិត្យមើលព័ត៌មានផ្ទាល់ខ្លួន កាលវិភាគសិក្សា និងតារាងវត្តមានក្នុង Dashboard។</td>
                      </tr>
                      <tr>
                        <td width="24" valign="top" style="font-weight: 800;">៣.</td>
                        <td>ចូលទៅកាន់ <strong>Settings (ការកំណត់)</strong> ដើម្បីផ្លាស់ប្តូរពាក្យសម្ងាត់ផ្ទាល់ខ្លួនរបស់ប្អូន។</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Security Advice Notice -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 14px 16px; margin-bottom: 20px;">
                <tr>
                  <td width="26" valign="top" style="font-size: 16px; color: #d97706; padding-right: 8px;">⚠️</td>
                  <td style="font-size: 12px; color: #92400e; line-height: 1.6;">
                    <strong>ការណែនាំអំពីសុវត្ថិភាព៖</strong> ពាក្យសម្ងាត់ខាងលើគឺជាពាក្យសម្ងាត់បណ្តោះអាសន្ន។ ដើម្បីការពារសុវត្ថិភាពគណនី សូមកុំចែករំលែកវាទៅកាន់អ្នកដទៃ។
                  </td>
                </tr>
              </table>

              <!-- Support Note -->
              <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.7;">
                ប្រសិនបើប្អូនមានចម្ងល់ ឬជួបបញ្ហាបច្ចេកទេសណាមួយ សូមទាក់ទងមកកាន់ការិយាល័យរដ្ឋបាលសាលារៀនដោយផ្ទាល់។
              </p>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #0f172a; padding: 26px 24px; text-align: center;">
              <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #f8fafc;">
                portal sala • សាលារៀន ជំនាន់ថ្មី PDT
              </p>
              <p style="margin: 0 0 12px 0; font-size: 11px; color: #94a3b8;">
                បណ្តុះបណ្តាលចំណេះដឹង បច្ចេកវិទ្យា និងនវានុវត្តន៍អប់រំជំនាន់ថ្មី
              </p>
              <p style="margin: 0; font-size: 11px; color: #64748b;">
                &copy; {{ date('Y') }} portal sala • សាលារៀន ជំនាន់ថ្មី. Developed by Phy Sophavat.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
