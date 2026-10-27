@extends('legal.layout')

@php
    // EDITA ESTOS DATOS con la información real del proyecto/empresa
    $empresa = 'Nexora [PentaByte S.A. de C.V.]';
    $correo = 'contacto@nexora.example';
    $jurisdiccion = 'Colima, México';
    $actualizado = '21 de octubre de 2026';
    $updatedEn = 'October 21, 2026';
    $es = app()->getLocale() === 'es';
@endphp

@section('title', $es ? 'Términos y Condiciones' : 'Terms and Conditions')

@section('content')
@if ($es)
    <h1>Términos y Condiciones de Uso</h1>
    <p class="legal-updated">Última actualización: {{ $actualizado }}</p>

    <p>Bienvenido a <strong>Nexora</strong>, un sistema de punto de venta multisede para la gestión de ventas, inventario y sucursales. Al crear una cuenta o utilizar la plataforma, declaras haber leído y aceptado estos Términos y Condiciones. Si no estás de acuerdo, no debes registrarte ni usar el servicio.</p>

    <h2>1. Definiciones</h2>
    <ul>
        <li><strong>Plataforma o Servicio:</strong> el sistema Nexora y todas sus funcionalidades.</li>
        <li><strong>Responsable:</strong> {{ $empresa }}, quien ofrece y administra la Plataforma.</li>
        <li><strong>Usuario:</strong> la persona que se registra y utiliza la Plataforma.</li>
        <li><strong>Contenido del Usuario:</strong> la información que el Usuario captura en la Plataforma (productos, ventas, inventario, sucursales, empleados, entre otros).</li>
    </ul>

    <h2>2. Descripción del servicio</h2>
    <p>Nexora permite registrar ventas, administrar inventarios, gestionar varias sucursales y consultar información operativa de un negocio. El Responsable puede modificar, ampliar o suspender funcionalidades para mejorar el servicio.</p>

    <h2>3. Requisitos para registrarse</h2>
    <ul>
        <li>Ser mayor de 18 años y tener capacidad legal para obligarse.</li>
        <li>Proporcionar información veraz, completa y actualizada.</li>
        <li>Contar con un correo electrónico válido. También puedes registrarte mediante tu cuenta de Google, sujeto a los términos de ese proveedor.</li>
        <li>Aceptar estos Términos y el Aviso de Privacidad. Sin esta aceptación no es posible crear la cuenta.</li>
    </ul>

    <h2>4. Cuenta y seguridad</h2>
    <ul>
        <li>Eres responsable de mantener la confidencialidad de tu contraseña y de toda actividad realizada desde tu cuenta.</li>
        <li>La contraseña debe cumplir los requisitos de seguridad indicados en el registro.</li>
        <li>Debes avisar de inmediato ante cualquier uso no autorizado de tu cuenta.</li>
        <li>Las cuentas creadas mediante el registro público tienen perfil de administrador de su propia operación, por lo que eres responsable de los permisos que otorgues a otras personas.</li>
    </ul>

    <h2>5. Uso aceptable</h2>
    <p>El Usuario se compromete a no:</p>
    <ul>
        <li>Usar la Plataforma para fines ilegales o fraudulentos.</li>
        <li>Intentar acceder sin autorización a cuentas, sistemas o datos de otros usuarios.</li>
        <li>Introducir virus, código malicioso o realizar acciones que afecten el funcionamiento o la seguridad del servicio.</li>
        <li>Copiar, descompilar, revender o explotar la Plataforma sin autorización por escrito.</li>
        <li>Registrar información de terceros sin contar con la facultad o el consentimiento necesarios.</li>
    </ul>

    <h2>6. Contenido y datos del Usuario</h2>
    <p>La información capturada en la Plataforma sigue siendo del Usuario. El Usuario es responsable de su exactitud y licitud, y otorga al Responsable la autorización necesaria para almacenarla y procesarla con el único fin de prestar el servicio. El tratamiento de datos personales se rige por el <a href="{{ url('/privacidad') }}">Aviso de Privacidad</a>.</p>

    <h2>7. Propiedad intelectual</h2>
    <p>El software, diseño, logotipos, marcas, textos y demás elementos de Nexora son propiedad del Responsable o de sus licenciantes y están protegidos por la legislación aplicable. Estos Términos no transfieren derechos de propiedad; solo otorgan una licencia limitada, personal, revocable y no exclusiva para usar la Plataforma.</p>

    <h2>8. Disponibilidad y respaldos</h2>
    <p>Se procura mantener el servicio disponible de forma continua, pero pueden existir interrupciones por mantenimiento, fallas técnicas o causas ajenas al Responsable. Se recomienda al Usuario conservar respaldos de la información que considere crítica.</p>

    <h2>9. Limitación de responsabilidad</h2>
    <p>En la medida permitida por la ley, el Responsable no será responsable por pérdidas indirectas, lucro cesante, pérdida de datos o daños derivados del uso o la imposibilidad de uso del servicio, ni por decisiones comerciales tomadas con base en la información de la Plataforma. Tampoco responde por fallas de servicios de terceros, como proveedores de internet o de autenticación.</p>

    <h2>10. Suspensión y terminación</h2>
    <p>El Responsable puede suspender o cancelar cuentas que incumplan estos Términos o la ley. El Usuario puede dejar de usar la Plataforma y solicitar la cancelación de su cuenta en cualquier momento mediante el correo de contacto.</p>

    <h2>11. Modificaciones</h2>
    <p>Estos Términos pueden actualizarse. Los cambios se publicarán en esta página con su fecha de actualización. El uso continuado de la Plataforma después de los cambios implica su aceptación.</p>

    <h2>12. Legislación aplicable y jurisdicción</h2>
    <p>Estos Términos se rigen por las leyes aplicables de los Estados Unidos Mexicanos. Para su interpretación y cumplimiento, las partes se someten a los tribunales competentes de {{ $jurisdiccion }}, renunciando a cualquier otro fuero que pudiera corresponderles.</p>

    <h2>13. Contacto</h2>
    <p>Para dudas o aclaraciones sobre estos Términos, escribe a <a href="mailto:{{ $correo }}">{{ $correo }}</a>.</p>

@else
    <h1>Terms and Conditions of Use</h1>
    <p class="legal-updated">Last updated: {{ $updatedEn }}</p>

    <p>Welcome to <strong>Nexora</strong>, a multi-branch point of sale system for managing sales, inventory and branches. By creating an account or using the platform, you declare that you have read and accepted these Terms and Conditions. If you do not agree, you must not register or use the service.</p>

    <h2>1. Definitions</h2>
    <ul>
        <li><strong>Platform or Service:</strong> the Nexora system and all its features.</li>
        <li><strong>Controller:</strong> {{ $empresa }}, who provides and manages the Platform.</li>
        <li><strong>User:</strong> the person who registers and uses the Platform.</li>
        <li><strong>User Content:</strong> the information the User enters into the Platform (products, sales, inventory, branches, employees, among others).</li>
    </ul>

    <h2>2. Description of the service</h2>
    <p>Nexora allows you to record sales, manage inventory, administer multiple branches and review a business's operational information. The Controller may modify, expand or suspend features to improve the service.</p>

    <h2>3. Registration requirements</h2>
    <ul>
        <li>Be over 18 years old and have legal capacity to enter into agreements.</li>
        <li>Provide truthful, complete and up-to-date information.</li>
        <li>Have a valid email address. You may also register with your Google account, subject to that provider's terms.</li>
        <li>Accept these Terms and the Privacy Notice. Without this acceptance, the account cannot be created.</li>
    </ul>

    <h2>4. Account and security</h2>
    <ul>
        <li>You are responsible for keeping your password confidential and for all activity carried out from your account.</li>
        <li>Your password must meet the security requirements shown at registration.</li>
        <li>You must notify us immediately of any unauthorized use of your account.</li>
        <li>Accounts created through public registration have administrator privileges over their own operation, so you are responsible for the permissions you grant to other people.</li>
    </ul>

    <h2>5. Acceptable use</h2>
    <p>The User agrees not to:</p>
    <ul>
        <li>Use the Platform for illegal or fraudulent purposes.</li>
        <li>Attempt to gain unauthorized access to accounts, systems or data of other users.</li>
        <li>Introduce viruses or malicious code, or take actions that affect the operation or security of the service.</li>
        <li>Copy, decompile, resell or exploit the Platform without written authorization.</li>
        <li>Enter third-party information without having the authority or necessary consent.</li>
    </ul>

    <h2>6. User content and data</h2>
    <p>The information entered into the Platform remains the User's. The User is responsible for its accuracy and lawfulness, and grants the Controller the authorization needed to store and process it solely to provide the service. The processing of personal data is governed by the <a href="{{ url('/privacidad') }}">Privacy Notice</a>.</p>

    <h2>7. Intellectual property</h2>
    <p>The software, design, logos, trademarks, texts and other elements of Nexora belong to the Controller or its licensors and are protected by applicable law. These Terms do not transfer ownership rights; they only grant a limited, personal, revocable and non-exclusive license to use the Platform.</p>

    <h2>8. Availability and backups</h2>
    <p>We strive to keep the service continuously available, but interruptions may occur due to maintenance, technical failures or causes beyond the Controller's control. Users are advised to keep backups of any information they consider critical.</p>

    <h2>9. Limitation of liability</h2>
    <p>To the extent permitted by law, the Controller is not liable for indirect losses, lost profits, data loss or damages arising from the use of, or inability to use, the service, nor for business decisions made based on the information in the Platform. It is also not responsible for failures of third-party services, such as internet or authentication providers.</p>

    <h2>10. Suspension and termination</h2>
    <p>The Controller may suspend or cancel accounts that breach these Terms or the law. The User may stop using the Platform and request account cancellation at any time through the contact email.</p>

    <h2>11. Changes</h2>
    <p>These Terms may be updated. Changes will be published on this page with their update date. Continued use of the Platform after the changes means you accept them.</p>

    <h2>12. Governing law and jurisdiction</h2>
    <p>These Terms are governed by the applicable laws of the United Mexican States. For their interpretation and enforcement, the parties submit to the competent courts of {{ $jurisdiccion }}, waiving any other jurisdiction that may apply.</p>

    <h2>13. Contact</h2>
    <p>For questions about these Terms, write to <a href="mailto:{{ $correo }}">{{ $correo }}</a>.</p>
@endif
@endsection