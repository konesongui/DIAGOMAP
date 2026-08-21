'send_payslip' => [
'template_id' => 'payslip_email',
'subject' => 'Bulletin de paie - {{payslip_month}} {{payslip_year}}',
'message' => '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: white; padding: 20px; border-radius: 5px; margin-top: 20px; }
        .footer { margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 5px; font-size: 12px; color: #666; }
        .payslip-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .signature { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>{{school_name}}</h2>
        <h3>Bulletin de Paie</h3>
    </div>

    <div class="content">
        <p>Cher/Chère <strong>{{staff_name}}</strong>,</p>

        <p>Veuillez trouver ci-joint votre bulletin de paie pour la période de <strong>{{payslip_month}} {{payslip_year}}</strong>.</p>

        <div class="payslip-info">
            <h4>Récapitulatif :</h4>
            <ul>
                <li><strong>Employé :</strong> {{staff_name}} ({{employee_id}})</li>
                <li><strong>Poste :</strong> {{designation}} - {{department}}</li>
                <li><strong>Période :</strong> {{payslip_month}} {{payslip_year}}</li>
                <li><strong>Salaire de base :</strong> {{basic_salary}} {{currency}}</li>
                <li><strong>Allocations :</strong> {{allowances}} {{currency}}</li>
                <li><strong>Retenues :</strong> {{deductions}} {{currency}}</li>
                <li><strong>Net à payer :</strong> <strong>{{net_salary}} {{currency}}</strong></li>
            </ul>
        </div>

        <p>Pour toute question concernant ce bulletin, veuillez répondre à cet email.</p>

        <div class="signature">
            <p>Cordialement,<br>
                <strong>{{user_name}}</strong><br>
                <em>{{school_name}}</em><br>
                Tél: {{school_phone}}</p>
        </div>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.<br>
            Adresse de réponse: {{user_email}}</p>
    </div>
</div>
</body>
</html>
',
'variables' => [
'{{staff_name}}', '{{employee_id}}', '{{payslip_month}}', '{{payslip_year}}',
'{{basic_salary}}', '{{allowances}}', '{{deductions}}', '{{net_salary}}',
'{{currency}}', '{{school_name}}', '{{school_phone}}', '{{school_address}}',
'{{designation}}', '{{department}}', '{{user_name}}', '{{user_email}}'
]
],