<div class="shadow-xl max-w-2xl mx-auto ">
    <div class="relative">

        <svg viewBox="0 0 200 200" class="absolute top-0 left-0 ">
            <defs>
                <linearGradient id="gradient1" x2="1" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
                <linearGradient id="gradient2" x2="1" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
                <linearGradient id="gradient3" x2="0.50" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
            </defs>
            <path class="gradient-bg" style="stroke: none; fill: url(#gradient1) #ff3300" opacity="0.5"
                  d="{{ $blobs[$step][0] }}"
                  transform="translate(100 100)"/>
            <path style="stroke: none; fill: url(#gradient2) #ff890c" opacity="1"
                  d="{{ $blobs[$step][1] }}"
                  transform="translate(100 100)"/>
            <path style="stroke: none; fill: url(#gradient3) #ff890c" opacity="0.5"
                  d="{{ $blobs[$step][2] }}"
                  transform="translate(100 100)"/>
        </svg>

        <div class="absolute top-16 sm:top-44 left-0 right-0 bottom-0  h-full flex flex-col items-center
                    text-center text-white">

            @if($step == 0)
                <div class="text-lg mt-auto p-8 font-semibold">Do you have a Group Token?</div>
                <div class="sm:mt-6">
                    <input wire:keydown.enter="checkToken" wire:model.lazy='token'
                           class="border-b-2 border-white bg-transparent">
                    <button class="hover:text-yellow-300 h-5 w-5" wire:click="checkToken">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="text-sm">
                        {{ $errorMessage }}
                    </div>
                </div>
                @if(!Auth::id())
                    <button class="hover:text-yellow-300 text-sm mt-16 sm:mt-24" wire:click="continueWithoutToken">
                        Continue without token
                    </button>
                @endif
            @elseif($step == 1)
                <div class="text-lg mt-auto p-8 font-semibold">
                    Accept Cookies and Cake
                </div>
                <div class="sm:mt-6 text-xs max-w-sm">
                    We require JavaScript.
                    We require cookies to keep you logged in.
                    We require a third party websocket to communicate the game states between different players.
                    Outside of your Game, your User Data is only used for debugging.
                    <br>
                    The cake is lie.
                    <button id="btn-modal" class="text-blue-500 mt-4">Read more</button>

                    <script>
                        document.getElementById('btn-modal').addEventListener('click', function () {
                            document.getElementById('overlay').classList.add('is-visible');
                            document.getElementById('modal').classList.add('is-visible');
                        });

                        document.getElementById('overlay').addEventListener('click', function () {
                            document.getElementById('overlay').classList.remove('is-visible');
                            document.getElementById('modal').classList.remove('is-visible');
                        });
                    </script>

                </div>
                <button class="hover:text-yellow-300 text-lg mt-12 sm:mt-20" wire:click="$set('step', 2)">
                    Accept
                </button>

            @elseif($step == 2 && $stepTwo == 'login')
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    @if($token)
                        <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'anonym')">
                            Play Anonymous?
                        </button>
                    @endif
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5"
                            wire:click="$set('stepTwo', 'register')">
                        Want an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Email</label>
                        <input wire:model.lazy='email' type="email"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Password</label>
                        <input wire:model.lazy='password' type="password"
                               wire:keydown.enter="checkLogin"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkLogin">
                    Login
                </button>
            @elseif($step == 2 && $stepTwo == 'anonym')
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'register')">
                        Want an Account?
                    </button>
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5" wire:click="$set('stepTwo', 'login')">
                        Have an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Name</label>
                        <input wire:model.lazy='name' wire:keydown.enter="checkAnonymousPlay"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkAnonymousPlay">
                    Play
                </button>
            @else
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    @if($token)
                        <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'anonym')">
                            Play Anonymous?
                        </button>
                    @endif
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5"
                            wire:click="$set('stepTwo', 'login')">
                        Have an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Name</label>
                        <input wire:model.lazy='name' class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Email</label>
                        <input wire:model.lazy='email' type="email"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Password</label>
                        <input wire:model.lazy='password' type="password"
                               wire:keydown.enter="checkRegister"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkRegister">
                    Register
                </button>
            @endif
        </div>
    </div>

    <style>
        .is-visible {
            visibility: visible;
            pointer-events: auto;
        }
    </style>

    <div class="bg-black opacity-50 w-screen h-screen absolute invisible left-0 top-0" id="overlay"></div>
    <div
        class="bg-white invisible rounded-lg shadow-lg
                absolute max-w-xl top-32 w-full left-0 right-0 mx-auto h-96
                p-4 text-center text-xs overflow-y-scroll"
        id="modal">
        <div>
            <h4 class="text-lg mt-4 font-semibold">Privacy Policy</h4>

            <div class="my-2 text-purple-600">
                Philodev here. Firstly, you are doing a good job on protecting your data and reading through this.
                Purple text will guide on your journey through this Privacy Policy and tell what info is not copy
                pasted legal information.
            </div>

            <p class="mt-2">We are very delighted that you have shown interest in our enterprise. Data protection is of
                a
                particularly
                high priority for the management of the Philodev (Sofia Fischer). The use of the Internet pages of the
                Philodev (Sofia Fischer) is possible without any indication of personal data; however, if a data subject
                wants to use special enterprise services via our website, processing of personal data could become
                necessary. If the processing of personal data is necessary and there is no statutory basis for such
                processing, we generally obtain consent from the data subject.</p>

            <p class="mt-2">The processing of personal data, such as the name, address, e-mail address, or telephone
                number of a data
                subject shall always be in line with the General Data Protection Regulation (GDPR), and in accordance
                with
                the country-specific data protection regulations applicable to the Philodev (Sofia Fischer). By means of
                this data protection declaration, our enterprise would like to inform the general public of the nature,
                scope, and purpose of the personal data we collect, use and process. Furthermore, data subjects are
                informed, by means of this data protection declaration, of the rights to which they are entitled.</p>

            <p class="mt-2">As the controller, the Philodev (Sofia Fischer) has implemented numerous technical and
                organizational
                measures to ensure the most complete protection of personal data processed through this website.
                However,
                Internet-based data transmissions may in principle have security gaps, so absolute protection may not be
                guaranteed.</p>

            <h4 class="text-lg mt-4 font-semibold">1. Definitions</h4>
            <p class="mt-2">The data protection declaration of the Philodev (Sofia Fischer) is based on the terms used
                by the
                European
                legislator for the adoption of the General Data Protection Regulation (GDPR). Our data protection
                declaration should be legible and understandable for the general public, as well as our customers and
                business partners. To ensure this, we would like to first explain the terminology used.</p>

            <p class="mt-2">In this data protection declaration, we use, inter alia, the following terms:</p>

            <ul style="list-style: none">
                <li><h4 class="text-lg mt-4 font-semibold">a)    Personal data</h4>
                    <p class="mt-2">Personal data means any information relating to an identified or identifiable
                        natural person
                        (“data
                        subject”). An identifiable natural person is one who can be identified, directly or indirectly,
                        in
                        particular by reference to an identifier such as a name, an identification number, location
                        data, an
                        online identifier or to one or more factors specific to the physical, physiological, genetic,
                        mental, economic, cultural or social identity of that natural person.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">b) Data subject</h4>
                    <p class="mt-2">Data subject is any identified or identifiable natural person, whose personal data
                        is processed
                        by
                        the controller responsible for the processing.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">c)    Processing</h4>
                    <p class="mt-2">Processing is any operation or set of operations which is performed on personal data
                        or on sets
                        of
                        personal data, whether or not by automated means, such as collection, recording, organisation,
                        structuring, storage, adaptation or alteration, retrieval, consultation, use, disclosure by
                        transmission, dissemination or otherwise making available, alignment or combination,
                        restriction,
                        erasure or destruction. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">d)    Restriction of processing</h4>
                    <p class="mt-2">Restriction of processing is the marking of stored personal data with the aim of
                        limiting their
                        processing in the future. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">e)    Profiling</h4>
                    <p class="mt-2">Profiling means any form of automated processing of personal data consisting of the
                        use of
                        personal
                        data to evaluate certain personal aspects relating to a natural person, in particular to analyse
                        or
                        predict aspects concerning that natural person's performance at work, economic situation,
                        health,
                        personal preferences, interests, reliability, behaviour, location or movements. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">f)     Pseudonymisation</h4>
                    <p class="mt-2">Pseudonymisation is the processing of personal data in such a manner that the
                        personal data can
                        no
                        longer be attributed to a specific data subject without the use of additional information,
                        provided
                        that such additional information is kept separately and is subject to technical and
                        organisational
                        measures to ensure that the personal data are not attributed to an identified or identifiable
                        natural person. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">g)    Controller responsible for the
                        processing</h4>
                    <p class="mt-2">Controller responsible for the processing is the natural or legal person, public
                        authority, agency or other body which, alone or jointly with others, determines the purposes and
                        means of the processing of personal data; where the purposes and means of such processing are
                        determined by Union or Member State law, the controller or the specific criteria for its
                        nomination
                        may be provided for by Union or Member State law. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">h)    Processor</h4>
                    <p class="mt-2">Processor is a natural or legal person, public authority, agency or other body which
                        processes
                        personal data on behalf of the controller. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">i)      Recipient</h4>
                    <p class="mt-2">Recipient is a natural or legal person, public authority, agency or another body, to
                        which the
                        personal data are disclosed, whether a third party or not. However, public authorities which may
                        receive personal data in the framework of a particular inquiry in accordance with Union or
                        Member
                        State law shall not be regarded as recipients; the processing of those data by those public
                        authorities shall be in compliance with the applicable data protection rules according to the
                        purposes of the processing. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">j)      Third party</h4>
                    <p class="mt-2">Third party is a natural or legal person, public authority, agency or body other
                        than the data
                        subject, controller, processor and persons who, under the direct authority of the controller or
                        processor, are authorised to process personal data.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">k)    Consent</h4>
                    <p class="mt-2">Consent of the data subject is any freely given, specific, informed and unambiguous
                        indication of
                        the
                        data subject's wishes by which he or she, by a statement or by a clear affirmative action,
                        signifies
                        agreement to the processing of personal data relating to him or her. </p>
                </li>
            </ul>

            <h4 class="text-lg mt-4 font-semibold">2. Name and Address of the controller</h4>
            <p class="mt-2">Controller for the purposes of the General Data Protection Regulation (GDPR), other data
                protection laws
                applicable in Member states of the European Union and other provisions related to data protection is:

            </p>

            <p class="mt-2">Philodev (Sofia Fischer)</p>
            <p class="mt-2">Munich</p>
            <p class="mt-2">Germany</p>
            <p class="mt-2">Email: sofia.k.christ@gmail.com</p>
            <p class="mt-2">Website: www.round-robin.philodev.one</p>

            <h4 class="text-lg mt-4 font-semibold">3. Cookies</h4>
            <p class="mt-2">The Internet pages of the Philodev (Sofia Fischer) use cookies. Cookies are text files that
                are stored in
                a computer system via an Internet browser.</p>

            <div class="my-2 text-purple-600">
                There is one cookie stored while you are here. It handles automatic login (if you are not staying away
                for too long).
                This also works if you don't want to enter an email address. If you delete the cookie or don't accept it
                in the first place,
                nothing bad will happen, but you will have to login every time you visit the Round Robin.
            </div>

            <p class="mt-2">Many Internet sites and servers use cookies. Many cookies contain a so-called cookie ID. A
                cookie ID is a
                unique identifier of the cookie. It consists of a character string through which Internet pages and
                servers
                can be assigned to the specific Internet browser in which the cookie was stored. This allows visited
                Internet sites and servers to differentiate the individual browser of the dats subject from other
                Internet
                browsers that contain other cookies. A specific Internet browser can be recognized and identified using
                the unique cookie ID.</p>

            <p class="mt-2">Through the use of cookies, the Philodev (Sofia Fischer) can provide the users of this
                website with more
                user-friendly services that would not be possible without the cookie setting.</p>

            <p class="mt-2">By means of a cookie, the information and offers on our website can be optimized with the
                user in mind.
                Cookies allow us, as previously mentioned, to recognize our website users. The purpose of this
                recognition
                is to make it easier for users to utilize our website. The website user that uses cookies, e.g. does not
                have to enter access data each time the website is accessed, because this is taken over by the website,
                and the cookie is thus stored on the user's computer system.</p>

            <p class="mt-2">The data subject may, at any time, prevent the setting of cookies through our website by
                means of a
                corresponding setting of the Internet browser used, and may thus permanently deny the setting of
                cookies.
                Furthermore, already set cookies may be deleted at any time via an Internet browser or other software
                programs. This is possible in all popular Internet browsers. If the data subject deactivates the setting
                of cookies in the Internet browser used, not all functions of our website may be entirely usable.</p>

            <h4 class="text-lg mt-4 font-semibold">4. Collection of general data and information</h4>
            <div class="my-2 text-purple-600">
                The personal data I store is limited dto what you enter as email and name, as well as some session data
                I need to make sure that not a different Computer with the same cookie is using your account. This Data will
                be stored on my server and will only be send to <a href="https://rollbar.com/" class="text-blue-500">Rollbar</a>,
                which I use to handle Errors. This helps me to improve Round Robin but you can stop this by activating the
                'Do not Track' option in your Browser.
            </div>
            <div class="my-2 text-purple-600">
                The second Third Party Application I use is <a href="https://pusher.com/" class="text-blue-500">Pusher</a>.
                Pusher offers a Websocket Service, which I use to communicate Game States during the Game. If one player
                performs an action the information is send to all other players to update their view. You can limit this,
                but be prepared to reload that page often to play.
            </div>

            <p class="mt-2">The website of the Philodev (Sofia Fischer) collects a series of general data and
                information when a data
                subject or automated system calls up the website. This general data and information are stored in the
                server
                log files. Collected may be (1) the browser types and versions used, (2) the operating system used by
                the
                accessing system, (3) the website from which an accessing system reaches our website (so-called
                referrers),
                (4) the sub-websites, (5) the date and time of access to the Internet site, (6) an Internet protocol
                address
                (IP address), (7) the Internet service provider of the accessing system, and (8) any other similar data
                and information that may be used in the event of attacks on our information technology systems.</p>

            <p class="mt-2">When using these general data and information, the Philodev (Sofia Fischer) does not draw
                any conclusions
                about the data subject. Rather, this information is needed to (1) deliver the content of our website
                correctly, (2) optimize the content of our website as well as its advertisement, (3) ensure the
                long-term viability of our information technology systems and website technology, and (4) provide law
                enforcement
                authorities with the information necessary for criminal prosecution in case of a cyber-attack.
                Therefore, the Philodev (Sofia Fischer) analyzes anonymously collected data and information
                statistically, with the
                aim of increasing the data protection and data security of our enterprise, and to ensure an optimal
                level of
                protection for the personal data we process. The anonymous data of the server log files are stored
                separately from all personal data provided by a data subject.</p>

            <h4 class="text-lg mt-4 font-semibold">5. Registration on our website</h4>

            <div class="my-2 text-purple-600">
                Registering gives you the benefit of changing your username and to host own game rounds.
                Currently I don't use the email address for anything but identification, but I might send a little
                info about a new game or so in the future.
            </div>

            <p class="mt-2">The data subject has the possibility to register on the website of the controller with the
                indication of
                personal data. Which personal data are transmitted to the controller is determined by the respective
                input
                mask used for the registration. The personal data entered by the data subject are collected and stored
                exclusively for internal use by the controller, and for his own purposes. The controller may request
                transfer to one or more processors (e.g. a parcel service) that also uses personal data for an internal
                purpose which is attributable to the controller.</p>

            <p class="mt-2">By registering on the website of the controller, the IP address—assigned by the Internet
                service provider
                (ISP) and used by the data subject—date, and time of the registration are also stored. The storage of
                this
                data takes place against the background that this is the only way to prevent the misuse of our services,
                and, if necessary, to make it possible to investigate committed offenses. Insofar, the storage of this
                data
                is necessary to secure the controller. This data is not passed on to third parties unless there is a
                statutory obligation to pass on the data, or if the transfer serves the aim of criminal prosecution.
            </p>

            <p class="mt-2">The registration of the data subject, with the voluntary indication of personal data, is
                intended to
                enable
                the controller to offer the data subject contents or services that may only be offered to registered
                users
                due to the nature of the matter in question. Registered persons are free to change the personal data
                specified during the registration at any time, or to have them completely deleted from the data stock of
                the
                controller.</p>

            <p class="mt-2">The data controller shall, at any time, provide information upon request to each data
                subject as to what
                personal data are stored about the data subject. In addition, the data controller shall correct or erase
                personal data at the request or indication of the data subject, insofar as there are no statutory
                storage
                obligations. The entirety of the controller’s employees are available to the data subject in this
                respect as
                contact persons.</p>

            <h4 class="text-lg mt-4 font-semibold">6. Comments function in the blog on the website</h4>
            <p class="mt-2">The Philodev (Sofia Fischer) offers users the possibility to leave individual comments on
                individual blog
                contributions on a blog, which is on the website of the controller. A blog is a web-based,
                publicly-accessible portal, through which one or more people called bloggers or web-bloggers may post
                articles or write down thoughts in so-called blogposts. Blogposts may usually be commented by third
                parties.</p>

            <p class="mt-2">If a data subject leaves a comment on the blog published on this website, the comments made
                by the data
                subject are also stored and published, as well as information on the date of the commentary and on the
                user's (pseudonym) chosen by the data subject. In addition, the IP address assigned by the Internet
                service
                provider (ISP) to the data subject is also logged. This storage of the IP address takes place for
                security
                reasons, and in case the data subject violates the rights of third parties, or posts illegal content
                through
                a given comment. The storage of these personal data is, therefore, in the own interest of the data
                controller, so that he can exculpate in the event of an infringement. This collected personal data will
                not
                be passed to third parties, unless such a transfer is required by law or serves the aim of the defense
                of
                the data controller.</p>

            <h4 class="text-lg mt-4 font-semibold">7. Routine erasure and blocking of personal data</h4>

            <div class="my-2 text-purple-600">
                If you don't have an email address set, I will delete your account after one month. If you have, I will
                delete your account after 6 Month of not logging in. There is the possibility to delete your account
                yourself in the profile settings (upper right corner - only if you have an email address set).
            </div>

            <p class="mt-2">The data controller shall process and store the personal data of the data subject only for
                the period
                necessary to achieve the purpose of storage, or as far as this is granted by the European legislator or
                other legislators in laws or regulations to which the controller is subject to.</p>

            <p class="mt-2">If the storage purpose is not applicable, or if a storage period prescribed by the European
                legislator or
                another competent legislator expires, the personal data are routinely blocked or erased in accordance
                with
                legal requirements.</p>

            <h4 class="text-lg mt-4 font-semibold">8. Rights of the data subject</h4>
            <ul style="list-style: none;">
                <li><h4 class="text-lg mt-4 font-semibold">a) Right of confirmation</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to obtain
                        from the
                        controller the confirmation as to whether or not personal data concerning him or her are being
                        processed. If a data subject wishes to avail himself of this right of confirmation, he or she
                        may,
                        at any time, contact any employee of the controller.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">b) Right of access</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to obtain
                        from the
                        controller free information about his or her personal data stored at any time and a copy of this
                        information. Furthermore, the European directives and regulations grant the data subject access
                        to
                        the following information:</p>

                    <ul style="list-style: none;">
                        <li>the purposes of the processing;</li>
                        <li>the categories of personal data concerned;</li>
                        <li>the recipients or categories of recipients to whom the personal data have been or will be
                            disclosed, in particular recipients in third countries or international organisations;
                        </li>
                        <li>where possible, the envisaged period for which the personal data will be stored, or, if not
                            possible, the criteria used to determine that period;
                        </li>
                        <li>the existence of the right to request from the controller rectification or erasure of
                            personal
                            data, or restriction of processing of personal data concerning the data subject, or to
                            object to
                            such processing;
                        </li>
                        <li>the existence of the right to lodge a complaint with a supervisory authority;</li>
                        <li>where the personal data are not collected from the data subject, any available information
                            as to
                            their source;
                        </li>
                        <li>the existence of automated decision-making, including profiling, referred to in Article
                            22(1)
                            and (4) of the GDPR and, at least in those cases, meaningful information about the logic
                            involved, as well as the significance and envisaged consequences of such processing for the
                            data
                            subject.
                        </li>

                    </ul>
                    <p class="mt-2">Furthermore, the data subject shall have a right to obtain information as to whether
                        personal
                        data
                        are transferred to a third country or to an international organisation. Where this is the case,
                        the
                        data subject shall have the right to be informed of the appropriate safeguards relating to the
                        transfer.</p>

                    <p class="mt-2">If a data subject wishes to avail himself of this right of access, he or she may, at
                        any time,
                        contact any employee of the controller.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">c) Right to rectification </h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to obtain
                        from the
                        controller without undue delay the rectification of inaccurate personal data concerning him or
                        her.
                        Taking into account the purposes of the processing, the data subject shall have the right to
                        have
                        incomplete personal data completed, including by means of providing a supplementary
                        statement.</p>

                    <p class="mt-2">If a data subject wishes to exercise this right to rectification, he or she may, at
                        any time,
                        contact
                        any employee of the controller.</p></li>
                <li>
                    <h4 class="text-lg mt-4 font-semibold">d) Right to erasure (Right to be forgotten) </h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to obtain
                        from the
                        controller the erasure of personal data concerning him or her without undue delay, and the
                        controller shall have the obligation to erase personal data without undue delay where one of the
                        following grounds applies, as long as the processing is not necessary: </p>

                    <ul style="list-style: none;">
                        <li>The personal data are no longer necessary in relation to the purposes for which they were
                            collected or otherwise processed.
                        </li>
                        <li>The data subject withdraws consent to which the processing is based according to point (a)
                            of
                            Article 6(1) of the GDPR, or point (a) of Article 9(2) of the GDPR, and where there is no
                            other
                            legal ground for the processing.
                        </li>
                        <li>The data subject objects to the processing pursuant to Article 21(1) of the GDPR and there
                            are
                            no overriding legitimate grounds for the processing, or the data subject objects to the
                            processing pursuant to Article 21(2) of the GDPR.
                        </li>
                        <li>The personal data have been unlawfully processed.</li>
                        <li>The personal data must be erased for compliance with a legal obligation in Union or Member
                            State
                            law to which the controller is subject.
                        </li>
                        <li>The personal data have been collected in relation to the offer of information society
                            services
                            referred to in Article 8(1) of the GDPR.
                        </li>

                    </ul>
                    <p class="mt-2">If one of the aforementioned reasons applies, and a data subject wishes to request
                        the erasure of
                        personal data stored by the Philodev (Sofia Fischer), he or she may, at any time, contact any
                        employee of the controller. An employee of Philodev (Sofia Fischer) shall promptly ensure that
                        the
                        erasure request is complied with immediately.</p>

                    <p class="mt-2">Where the controller has made personal data public and is obliged pursuant to
                        Article 17(1) to
                        erase
                        the personal data, the controller, taking account of available technology and the cost of
                        implementation, shall take reasonable steps, including technical measures, to inform other
                        controllers processing the personal data that the data subject has requested erasure by such
                        controllers of any links to, or copy or replication of, those personal data, as far as
                        processing is
                        not required. An employees of the Philodev (Sofia Fischer) will arrange the necessary measures
                        in
                        individual cases.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">e) Right of restriction of processing</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to obtain
                        from the
                        controller restriction of processing where one of the following applies:</p>

                    <ul style="list-style: none;">
                        <li>The accuracy of the personal data is contested by the data subject, for a period enabling
                            the
                            controller to verify the accuracy of the personal data.
                        </li>
                        <li>The processing is unlawful and the data subject opposes the erasure of the personal data and
                            requests instead the restriction of their use instead.
                        </li>
                        <li>The controller no longer needs the personal data for the purposes of the processing, but
                            they
                            are required by the data subject for the establishment, exercise or defence of legal claims.
                        </li>
                        <li>The data subject has objected to processing pursuant to Article 21(1) of the GDPR pending
                            the
                            verification whether the legitimate grounds of the controller override those of the data
                            subject.
                        </li>

                    </ul>
                    <p class="mt-2">If one of the aforementioned conditions is met, and a data subject wishes to request
                        the
                        restriction
                        of the processing of personal data stored by the Philodev (Sofia Fischer), he or she may at any
                        time
                        contact any employee of the controller. The employee of the Philodev (Sofia Fischer) will
                        arrange
                        the restriction of the processing. </p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">f) Right to data portability</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator, to
                        receive the
                        personal
                        data concerning him or her, which was provided to a controller, in a structured, commonly used
                        and
                        machine-readable format. He or she shall have the right to transmit those data to another
                        controller
                        without hindrance from the controller to which the personal data have been provided, as long as
                        the
                        processing is based on consent pursuant to point (a) of Article 6(1) of the GDPR or point (a) of
                        Article 9(2) of the GDPR, or on a contract pursuant to point (b) of Article 6(1) of the GDPR,
                        and
                        the processing is carried out by automated means, as long as the processing is not necessary for
                        the
                        performance of a task carried out in the public interest or in the exercise of official
                        authority
                        vested in the controller.</p>

                    <p class="mt-2">Furthermore, in exercising his or her right to data portability pursuant to Article
                        20(1) of the
                        GDPR, the data subject shall have the right to have personal data transmitted directly from one
                        controller to another, where technically feasible and when doing so does not adversely affect
                        the
                        rights and freedoms of others.</p>

                    <p class="mt-2">In order to assert the right to data portability, the data subject may at any time
                        contact any
                        employee of the Philodev (Sofia Fischer).</p>
                </li>
                <li>
                    <h4 class="text-lg mt-4 font-semibold">g) Right to object</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to object,
                        on grounds
                        relating to his or her particular situation, at any time, to processing of personal data
                        concerning
                        him or her, which is based on point (e) or (f) of Article 6(1) of the GDPR. This also applies to
                        profiling based on these provisions.</p>

                    <p class="mt-2">The Philodev (Sofia Fischer) shall no longer process the personal data in the event
                        of the
                        objection,
                        unless we can demonstrate compelling legitimate grounds for the processing which override the
                        interests, rights and freedoms of the data subject, or for the establishment, exercise or
                        defence of
                        legal claims.</p>

                    <p class="mt-2">If the Philodev (Sofia Fischer) processes personal data for direct marketing
                        purposes, the data
                        subject shall have the right to object at any time to processing of personal data concerning him
                        or
                        her for such marketing. This applies to profiling to the extent that it is related to such
                        direct
                        marketing. If the data subject objects to the Philodev (Sofia Fischer) to the processing for
                        direct
                        marketing purposes, the Philodev (Sofia Fischer) will no longer process the personal data for
                        these
                        purposes.</p>

                    <p class="mt-2">In addition, the data subject has the right, on grounds relating to his or her
                        particular
                        situation,
                        to object to processing of personal data concerning him or her by the Philodev (Sofia Fischer)
                        for
                        scientific or historical research purposes, or for statistical purposes pursuant to Article
                        89(1) of
                        the GDPR, unless the processing is necessary for the performance of a task carried out for
                        reasons
                        of public interest.</p>

                    <p class="mt-2">In order to exercise the right to object, the data subject may contact any employee
                        of the
                        Philodev
                        (Sofia Fischer). In addition, the data subject is free in the context of the use of information
                        society services, and notwithstanding Directive 2002/58/EC, to use his or her right to object by
                        automated means using technical specifications.</p>
                </li>
                <li><h4 class="text-lg mt-4 font-semibold">h) Automated individual decision-making, including
                        profiling</h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator not to be
                        subject to a
                        decision based solely on automated processing, including profiling, which produces legal effects
                        concerning him or her, or similarly significantly affects him or her, as long as the decision
                        (1) is
                        not is necessary for entering into, or the performance of, a contract between the data subject
                        and a
                        data controller, or (2) is not authorised by Union or Member State law to which the controller
                        is
                        subject and which also lays down suitable measures to safeguard the data subject's rights and
                        freedoms and legitimate interests, or (3) is not based on the data subject's explicit
                        consent.</p>

                    <p class="mt-2">If the decision (1) is necessary for entering into, or the performance of, a
                        contract between the
                        data subject and a data controller, or (2) it is based on the data subject's explicit consent,
                        the
                        Philodev (Sofia Fischer) shall implement suitable measures to safeguard the data subject's
                        rights
                        and freedoms and legitimate interests, at least the right to obtain human intervention on the
                        part
                        of the controller, to express his or her point of view and contest the decision.</p>

                    <p class="mt-2">If the data subject wishes to exercise the rights concerning automated individual
                        decision-making, he
                        or she may, at any time, contact any employee of the Philodev (Sofia Fischer).</p>

                </li>
                <li><h4 class="text-lg mt-4 font-semibold">i) Right to withdraw data protection consent </h4>
                    <p class="mt-2">Each data subject shall have the right granted by the European legislator to
                        withdraw his or her
                        consent to processing of his or her personal data at any time. </p>

                    <p class="mt-2">If the data subject wishes to exercise the right to withdraw the consent, he or she
                        may, at any
                        time,
                        contact any employee of the Philodev (Sofia Fischer).</p>

                </li>
            </ul>
            <h4 class="text-lg mt-4 font-semibold">9. Legal basis for the processing </h4>
            <p class="mt-2">Art. 6(1) lit. a GDPR serves as the legal basis for processing operations for which we
                obtain consent for
                a
                specific processing purpose. If the processing of personal data is necessary for the performance of a
                contract to which the data subject is party, as is the case, for example, when processing operations are
                necessary for the supply of goods or to provide any other service, the processing is based on Article
                6(1)
                lit. b GDPR. The same applies to such processing operations which are necessary for carrying out
                pre-contractual measures, for example in the case of inquiries concerning our products or services. Is
                our
                company subject to a legal obligation by which processing of personal data is required, such as for the
                fulfillment of tax obligations, the processing is based on Art. 6(1) lit. c GDPR.
                In rare cases, the processing of personal data may be necessary to protect the vital interests of the
                data
                subject or of another natural person. This would be the case, for example, if a visitor were injured in
                our
                company and his name, age, health insurance data or other vital information would have to be passed on
                to a
                doctor, hospital or other third party. Then the processing would be based on Art. 6(1) lit. d GDPR.
                Finally, processing operations could be based on Article 6(1) lit. f GDPR. This legal basis is used for
                processing operations which are not covered by any of the abovementioned legal grounds, if processing is
                necessary for the purposes of the legitimate interests pursued by our company or by a third party,
                except
                where such interests are overridden by the interests or fundamental rights and freedoms of the data
                subject
                which require protection of personal data. Such processing operations are particularly permissible
                because
                they have been specifically mentioned by the European legislator. He considered that a legitimate
                interest
                could be assumed if the data subject is a client of the controller (Recital 47 Sentence 2 GDPR).
            </p>

            <h4 class="text-lg mt-4 font-semibold">10. The legitimate interests pursued by the controller or by a third
                party</h4>
            <p class="mt-2">Where the processing of personal data is based on Article 6(1) lit. f GDPR our legitimate
                interest is to
                carry out our business in favor of the well-being of all our employees and the shareholders.</p>

            <h4 class="text-lg mt-4 font-semibold">11. Period for which the personal data will be stored</h4>
            <p class="mt-2">The criteria used to determine the period of storage of personal data is the respective
                statutory
                retention
                period. After expiration of that period, the corresponding data is routinely deleted, as long as it is
                no
                longer necessary for the fulfillment of the contract or the initiation of a contract.</p>

            <h4 class="text-lg mt-4 font-semibold">12. Provision of personal data as statutory or contractual
                requirement;
                Requirement necessary to enter into
                a contract; Obligation of the data subject to provide the personal data; possible consequences of
                failure to
                provide such data </h4>
            <p class="mt-2">We clarify that the provision of personal data is partly required by law (e.g. tax
                regulations) or can
                also
                result from contractual provisions (e.g. information on the contractual partner).

                Sometimes it may be necessary to conclude a contract that the data subject provides us with personal
                data,
                which must subsequently be processed by us. The data subject is, for example, obliged to provide us with
                personal data when our company signs a contract with him or her. The non-provision of the personal data
                would have the consequence that the contract with the data subject could not be concluded.

                Before personal data is provided by the data subject, the data subject must contact any employee. The
                employee clarifies to the data subject whether the provision of the personal data is required by law or
                contract or is necessary for the conclusion of the contract, whether there is an obligation to provide
                the
                personal data and the consequences of non-provision of the personal data.
            </p>

            <h4 class="text-lg mt-4 font-semibold">13. Existence of automated decision-making</h4>
            <p class="mt-2">As a responsible company, we do not use automatic decision-making or profiling.</p>

            <p class="mt-2">This Privacy Policy has been generated by the Privacy Policy Generator of the <a
                    href="https://dg-datenschutz.de/services/external-data-protection-officer/?lang=en">DGD - Your
                    External
                    DPO</a> that was developed in cooperation with <a href="https://www.wbs-law.de/eng/">German
                    Lawyers</a>
                from WILDE BEUGER SOLMECKE, Cologne.
            </p>
        </div>
    </div>
</div>
