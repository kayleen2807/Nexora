@extends('legal.layout')

@php
    // EDITA ESTOS DATOS con la información real del proyecto/empresa
    $empresa = 'Nexora[PentaByte S.A. de C.V.]';
    $domicilio = '[UNIVERSIDAD DE COLIMA 180, COL. CENTRO, COLIMA, COLIMA, MÉXICO]';
    $addressEn = '[MIRAMA-NARANO, MANZANILLO COLIMA, MÉXICO]';
    $correo = 'privacidad@nexora.example';
    $actualizado = '21 de octubre de 2026';
    $updatedEn = 'October 21, 2026';
    $es = app()->getLocale() === 'es';
@endphp

@section('title', $es ? 'Aviso de Privacidad' : 'Privacy Notice')

@section('content')
@if ($es)
    <h1>Aviso de Privacidad</h1>
    <p class="legal-updated">Última actualización: {{ $actualizado }}</p>

    <p>En cumplimiento de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares y demás normativa aplicable, se pone a tu disposición el presente Aviso de Privacidad de <strong>Nexora</strong>.</p>

    <h2>1. Identidad y domicilio del responsable</h2>
    <p><strong>{{ $empresa }}</strong>, con domicilio en calle {{ $domicilio }}, es responsable del tratamiento de tus datos personales.</p>

    <h2>2. Datos personales que recabamos</h2>
    <p>Para crear y administrar tu cuenta podemos recabar:</p>
    <ul>
        <li><strong>Datos de identificación:</strong> nombre y apellido.</li>
        <li><strong>Datos de contacto:</strong> correo electrónico.</li>
        <li><strong>Datos personales de edad:</strong> fecha de nacimiento (para verificar que eres mayor de 18 años).</li>
        <li><strong>Datos de acceso:</strong> contraseña, que se almacena de forma cifrada (nunca en texto plano).</li>
        <li><strong>Datos de tu cuenta de Google</strong>, si eliges registrarte o iniciar sesión con ese medio: nombre y correo electrónico que Google nos comparta.</li>
        <li><strong>Datos de uso y técnicos:</strong> información de sesión y de navegación necesaria para el funcionamiento y la seguridad del sistema, y tus preferencias de idioma y tema (claro/oscuro).</li>
    </ul>
    <p>No solicitamos datos personales sensibles. Tampoco recabamos datos de menores de edad, ya que el servicio es solo para personas mayores de 18 años.</p>

    <h2>3. Finalidades del tratamiento</h2>
    <p><strong>Finalidades primarias (necesarias para el servicio):</strong></p>
    <ul>
        <li>Crear, administrar y autenticar tu cuenta.</li>
        <li>Verificar que cumples con la mayoría de edad.</li>
        <li>Prestar las funcionalidades del sistema de punto de venta.</li>
        <li>Garantizar la seguridad de la cuenta y prevenir fraudes o accesos no autorizados.</li>
        <li>Enviarte comunicaciones esenciales del servicio, como recuperación de contraseña y avisos de seguridad.</li>
        <li>Cumplir obligaciones legales y atender requerimientos de autoridad competente.</li>
    </ul>
    <p><strong>Finalidades secundarias (opcionales):</strong></p>
    <ul>
        <li>Mejorar el servicio mediante análisis estadísticos con datos disociados.</li>
        <li>Informarte sobre novedades o mejoras de Nexora.</li>
    </ul>
    <p>Si no deseas que tus datos se traten para las finalidades secundarias, puedes manifestarlo escribiendo a <a href="mailto:{{ $correo }}">{{ $correo }}</a>. Tu negativa no afectará el acceso al servicio.</p>

    <h2>4. Información de tu negocio y de terceros</h2>
    <p>La información operativa que captures en la plataforma (ventas, inventario, sucursales, empleados, clientes) es tuya. Respecto de los datos personales de terceros que registres, tú eres quien decide su tratamiento y debes contar con la facultad o el consentimiento necesarios. Nexora solo la procesa para prestarte el servicio.</p>

    <h2>5. Transferencias y encargados</h2>
    <p>No vendemos tus datos personales. Podemos compartirlos únicamente con:</p>
    <ul>
        <li>Proveedores que nos apoyan a operar el servicio (alojamiento, infraestructura, envío de correos), quienes actúan bajo nuestras instrucciones y obligaciones de confidencialidad.</li>
        <li>Google, cuando eliges acceder con tu cuenta de Google.</li>
        <li>Autoridades competentes, cuando exista un requerimiento legal.</li>
    </ul>
    <p>Salvo los casos previstos en la ley, cualquier otra transferencia requerirá tu consentimiento.</p>

    <h2>6. Medidas de seguridad</h2>
    <p>Aplicamos medidas administrativas, técnicas y físicas razonables para proteger tus datos contra daño, pérdida, alteración, acceso o uso no autorizado, incluyendo el cifrado de contraseñas y la protección de las sesiones. Ningún sistema es infalible, por lo que te recomendamos usar una contraseña robusta y no compartirla.</p>

    <h2>7. Derechos ARCO y revocación del consentimiento</h2>
    <p>Tienes derecho a <strong>Acceder</strong> a tus datos, <strong>Rectificarlos</strong> si son inexactos, <strong>Cancelarlos</strong> cuando consideres que no se requieren para las finalidades señaladas u <strong>Oponerte</strong> a su tratamiento para fines específicos. También puedes <strong>revocar tu consentimiento</strong> en cualquier momento.</p>
    <p>Para ejercerlos, envía una solicitud a <a href="mailto:{{ $correo }}">{{ $correo }}</a> que incluya:</p>
    <ol>
        <li>Tu nombre y correo electrónico registrado, para comunicarte la respuesta.</li>
        <li>Documento que acredite tu identidad o, en su caso, la representación legal.</li>
        <li>Descripción clara de los datos y del derecho que deseas ejercer.</li>
        <li>Cualquier elemento que facilite localizar tus datos.</li>
    </ol>
    <p>Responderemos en los plazos que marca la ley. La revocación o cancelación puede implicar que ya no podamos prestarte el servicio.</p>

    <h2>8. Cookies y tecnologías similares</h2>
    <p>Usamos cookies de sesión y almacenamiento local del navegador para mantener tu sesión iniciada, proteger los formularios contra solicitudes falsificadas y recordar tu idioma y tema. Puedes bloquearlas o eliminarlas desde la configuración de tu navegador, aunque algunas funciones podrían dejar de operar correctamente.</p>

    <h2>9. Conservación de los datos</h2>
    <p>Conservaremos tus datos mientras tu cuenta esté activa y durante el tiempo necesario para cumplir obligaciones legales. Después serán bloqueados y suprimidos conforme a la normativa aplicable.</p>

    <h2>10. Cambios al aviso</h2>
    <p>Este aviso puede modificarse por cambios legales o del servicio. Publicaremos la versión vigente en esta página, indicando su fecha de actualización.</p>

    <h2>11. Contacto y autoridad</h2>
    <p>Para dudas sobre este aviso o el tratamiento de tus datos, escribe a <a href="mailto:{{ $correo }}">{{ $correo }}</a>. Si consideras que tu derecho a la protección de datos personales ha sido vulnerado, puedes acudir a la autoridad competente en la materia.</p>

    <div class="legal-note">Al marcar la casilla de aceptación en el registro, confirmas que has leído este Aviso de Privacidad y que consientes el tratamiento de tus datos conforme a él.</div>

@else
    <h1>Privacy Notice</h1>
    <p class="legal-updated">Last updated: {{ $updatedEn }}</p>

    <p>In compliance with the Mexican Federal Law on the Protection of Personal Data Held by Private Parties and other applicable regulations, this Privacy Notice for <strong>Nexora</strong> is made available to you.</p>

    <h2>1. Identity and address of the controller</h2>
    <p><strong>{{ $empresa }}</strong>, located at street {{ $addressEn }}, is responsible for the processing of your personal data.</p>

    <h2>2. Personal data we collect</h2>
    <p>To create and manage your account we may collect:</p>
    <ul>
        <li><strong>Identification data:</strong> first name and surname.</li>
        <li><strong>Contact data:</strong> email address.</li>
        <li><strong>Age data:</strong> date of birth (to verify that you are over 18).</li>
        <li><strong>Access data:</strong> password, stored in encrypted form (never as plain text).</li>
        <li><strong>Google account data</strong>, if you choose to register or sign in that way: the name and email address Google shares with us.</li>
        <li><strong>Usage and technical data:</strong> session and browsing information needed for the system's operation and security, and your language and theme (light/dark) preferences.</li>
    </ul>
    <p>We do not request sensitive personal data. We also do not collect data from minors, as the service is only for people over 18.</p>

    <h2>3. Purposes of processing</h2>
    <p><strong>Primary purposes (necessary for the service):</strong></p>
    <ul>
        <li>Create, manage and authenticate your account.</li>
        <li>Verify that you meet the age requirement.</li>
        <li>Provide the point of sale system's features.</li>
        <li>Ensure account security and prevent fraud or unauthorized access.</li>
        <li>Send you essential service communications, such as password recovery and security notices.</li>
        <li>Comply with legal obligations and respond to requests from competent authorities.</li>
    </ul>
    <p><strong>Secondary purposes (optional):</strong></p>
    <ul>
        <li>Improve the service through statistical analysis using dissociated data.</li>
        <li>Inform you about Nexora news or improvements.</li>
    </ul>
    <p>If you do not want your data processed for secondary purposes, you can let us know by writing to <a href="mailto:{{ $correo }}">{{ $correo }}</a>. Your refusal will not affect your access to the service.</p>

    <h2>4. Business and third-party information</h2>
    <p>The operational information you enter into the platform (sales, inventory, branches, employees, customers) is yours. For the personal data of third parties that you register, you decide how it is processed and must have the authority or necessary consent. Nexora only processes it to provide you with the service.</p>

    <h2>5. Transfers and processors</h2>
    <p>We do not sell your personal data. We may share it only with:</p>
    <ul>
        <li>Providers that help us operate the service (hosting, infrastructure, email delivery), who act under our instructions and confidentiality obligations.</li>
        <li>Google, when you choose to sign in with your Google account.</li>
        <li>Competent authorities, when there is a legal requirement.</li>
    </ul>
    <p>Except in cases provided by law, any other transfer will require your consent.</p>

    <h2>6. Security measures</h2>
    <p>We apply reasonable administrative, technical and physical measures to protect your data against damage, loss, alteration, and unauthorized access or use, including password encryption and session protection. No system is infallible, so we recommend using a strong password and not sharing it.</p>

    <h2>7. ARCO rights and withdrawal of consent</h2>
    <p>You have the right to <strong>Access</strong> your data, <strong>Rectify</strong> it if inaccurate, <strong>Cancel</strong> it when you believe it is not required for the stated purposes, or <strong>Object</strong> to its processing for specific purposes. You may also <strong>withdraw your consent</strong> at any time.</p>
    <p>To exercise them, send a request to <a href="mailto:{{ $correo }}">{{ $correo }}</a> including:</p>
    <ol>
        <li>Your name and registered email address, so we can reply.</li>
        <li>A document proving your identity or, where applicable, legal representation.</li>
        <li>A clear description of the data and the right you wish to exercise.</li>
        <li>Any element that helps us locate your data.</li>
    </ol>
    <p>We will respond within the deadlines set by law. Withdrawal or cancellation may mean we can no longer provide you with the service.</p>

    <h2>8. Cookies and similar technologies</h2>
    <p>We use session cookies and browser local storage to keep you signed in, protect forms against forged requests, and remember your language and theme. You can block or delete them in your browser settings, although some features may stop working properly.</p>

    <h2>9. Data retention</h2>
    <p>We will keep your data while your account is active and for the time needed to comply with legal obligations. Afterwards it will be blocked and deleted in accordance with applicable regulations.</p>

    <h2>10. Changes to this notice</h2>
    <p>This notice may be modified due to legal or service changes. We will publish the current version on this page, indicating its update date.</p>

    <h2>11. Contact and authority</h2>
    <p>For questions about this notice or the processing of your data, write to <a href="mailto:{{ $correo }}">{{ $correo }}</a>. If you believe your right to personal data protection has been violated, you may go to the competent authority on the matter.</p>

    <div class="legal-note">By checking the acceptance box at registration, you confirm that you have read this Privacy Notice and consent to the processing of your data in accordance with it.</div>
@endif
@endsection