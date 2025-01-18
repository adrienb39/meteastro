document.addEventListener("DOMContentLoaded", function () {
  const cookieWrapper = document.querySelector(".cookie-wrapper");
  const acceptAllButton = document.getElementById("accept-all");
  const rejectAllButton = document.getElementById("reject-all");
  const managePreferencesButton = document.getElementById("manage-preferences");
  const savePreferencesButton = document.getElementById("save-preferences");

  const functionalCookiesCheckbox = document.getElementById("functional-cookies");
  const analyticsCookiesCheckbox = document.getElementById("analytics-cookies");
  const advertisingCookiesCheckbox = document.getElementById("advertising-cookies");

  const modal = document.getElementById("popup-policy");
  const closeModalButton = document.querySelector(".close-modal");

  const cookieConsentKey = "cookieByMeteastro";

  // Vérifie si l'utilisateur a déjà donné son consentement
  if (document.cookie.includes(cookieConsentKey)) {
    cookieWrapper.style.display = "none";
  } else {
    cookieWrapper.style.display = "flex";
  }

  // Accepter tous les cookies
  acceptAllButton.addEventListener("click", function () {
    document.cookie = `${cookieConsentKey}=accepted; max-age=${60 * 60 * 24 * 30}`; // valable 30 jours
    cookieWrapper.style.display = "none";
  });

  // Refuser tous les cookies
  rejectAllButton.addEventListener("click", function () {
    document.cookie = `${cookieConsentKey}=rejected; max-age=${60 * 60 * 24 * 30}`; // valable 30 jours
    cookieWrapper.style.display = "none";
  });

  // Gérer les préférences
  managePreferencesButton.addEventListener("click", function () {
    modal.style.display = "flex";
  });

  // Enregistrer les préférences
  savePreferencesButton.addEventListener("click", function () {
    const acceptedCookies = {
      functional: functionalCookiesCheckbox.checked,
      analytics: analyticsCookiesCheckbox.checked,
      advertising: advertisingCookiesCheckbox.checked
    };

    // Exemple de cookie personnalisé (stockage des choix)
    document.cookie = `${cookieConsentKey}=${JSON.stringify(acceptedCookies)}; max-age=${60 * 60 * 24 * 30}`;
    modal.style.display = "none";
    cookieWrapper.style.display = "none";
  });

  // Fermer le modal
  closeModalButton.addEventListener("click", function () {
    modal.style.display = "none";
  });

  // Lien "En savoir plus..." pour ouvrir le modal
  const moreInfoLink = document.getElementById("more-info-link");
  moreInfoLink.addEventListener("click", function (event) {
    event.preventDefault(); // Empêche le lien de se comporter normalement
    modal.style.display = "flex"; // Affiche le modal
  });
});