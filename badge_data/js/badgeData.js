// Funktion zum Erzeugen eines QR-Codes aus dem Textarea-Inhalt
function generateTextareaQRCode() {
    var textarea = document.getElementById('JSON');
    if (!textarea) {
        console.error('Error: Textarea element was not found in the DOM.');
        return;
    }

    var textareaContent = textarea.value;
    if (textareaContent) {
        var qrCodeContainer = document.getElementById('textarea-qr-code');
        qrCodeContainer.innerHTML = '';

        var qr = qrcode(0, 'L');
        qr.addData(textareaContent);
        qr.make();
        qrCodeContainer.innerHTML = qr.createImgTag(4);
    }
}

function fillTextarea(badgeId, liElem) {
    if (!badgeId || !liElem) {
        console.error('Error: Invalid parameters passed to fillTextarea.');
        return;
    }

    var textarea = document.getElementById('JSON');
    var badgeData = JSONBadge[badgeId];
    if (!badgeData) {
        console.error('Error: Badge data not found for badge ID:', badgeId);
        return;
    }

    textarea.value = JSON.stringify(badgeData, null, "\t");
    generateTextareaQRCode();

    liElem.parentElement.children.forEach(function (elem) {
        elem.classList.remove('selected');
    });
    liElem.classList.add('selected');
    selectedBadgeId = badgeId;
}

function runCurl() {
    if (!createInvitationUrl) {
        alert('Error: Invitation URL is not set.');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", createInvitationUrl, true);
    xhr.setRequestHeader("accept", "application/json");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.invitation) {
                    var qrCodeContainer = document.getElementById('qr-code');
                    qrCodeContainer.innerHTML = '';

                    var qr = qrcode(0, 'L');
                    qr.addData(JSON.stringify(response.invitation));
                    qr.make();
                    qrCodeContainer.innerHTML = qr.createImgTag(6);
                } else {
                    alert('Error: Invitation data is missing in the response.');
                }
            } else {
                alert('Error: Unable to execute cURL command with status ' + xhr.status);
            }
        }
    };

    xhr.onerror = function () {
        alert('Network error occurred during the HTTP request.');
    };

    xhr.send("{}");
}

function issueCredential() {
    if (!connectionsUrl) {
        console.error('Error: Connections URL is not set.');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", connectionsUrl, true);
    xhr.setRequestHeader("accept", "application/json");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.results && response.results.length > 0) {
                    connectionId = response.results[0].connection_id;
                    var selectedBadgeData = JSONBadge[selectedBadgeId];
                    if (!selectedBadgeData) {
                        alert("Error: No badge selected.");
                        return;
                    }
                    sendCredential(connectionId, selectedBadgeData);
                } else {
                    alert("Error: No connections found.");
                }
            } else {
                alert('Error: Unable to get connections with status ' + xhr.status);
            }
        }
    };

    xhr.onerror = function () {
        console.error('Network error occurred during the HTTP request.');
    };

    xhr.send();
}

function sendCredential(connectionId, badgeData) {
    if (!issueCredentialUrl) {
        console.error('Error: Issue credential URL is not set.');
        return;
    }

    var xhr2 = new XMLHttpRequest();
    xhr2.open("POST", issueCredentialUrl, true);
    xhr2.setRequestHeader("accept", "application/json");
    xhr2.setRequestHeader("Content-Type", "application/json");

    xhr2.onreadystatechange = function () {
        if (xhr2.readyState === XMLHttpRequest.DONE) {
            if (xhr2.status === 200) {
                alert("Badge Issued!");
            } else {
                console.error('Error: Unable to issue the badge with status:', xhr2.status);
            }
        }
    };

    xhr2.onerror = function () {
        console.error('Network error occurred during the HTTP request.');
    };

    var dataToSend = {
        "auto_remove": true,
        "comment": "Issuance of OpenBadge for French A1",
        "connection_id": connectionId,
        "credential_preview": {
            "@type": "issue-credential/2.0/credential-preview",
            "attributes": badgeData
        },
        "filter": {
            "indy": {
                "cred_def_id": credentialDefinitionId,
                "schema_id": schemaId
            }
        },
        "trace": false
    };

    xhr2.send(JSON.stringify(dataToSend));
}
