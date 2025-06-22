# Fixing Google Sign-In Issues

This guide will help you resolve the Google Sign-In issues in your application.

## 1. Enable the People API (REQUIRED)

The main error is related to the People API being disabled. To fix this:

1. Go to [Google Cloud Console](https://console.developers.google.com/apis/api/people.googleapis.com/overview?project=244660030535)
2. Click **Enable** to activate the People API for your project
3. Wait 2-5 minutes for the changes to propagate through Google's systems

## 2. CORS and Security Headers (COMPLETED)

We've updated the `.htaccess` file with proper CORS and security headers:

-   Added `Cross-Origin-Opener-Policy: same-origin-allow-popups` to fix popup issues
-   Updated other security headers for better compatibility

## 3. Verify OAuth Consent Screen Configuration

1. Go to [OAuth Consent Screen](https://console.cloud.google.com/apis/credentials/consent?project=244660030535)
2. Ensure the following scopes are added:
    - `https://www.googleapis.com/auth/userinfo.email`
    - `https://www.googleapis.com/auth/userinfo.profile`
    - `openid`

## 4. Check Authorized Domains

1. In the OAuth Consent Screen, verify your domain is listed under "Authorized domains"
2. For local development, ensure `localhost` is included

## 5. Verify OAuth Client ID Settings

1. Go to [Credentials](https://console.cloud.google.com/apis/credentials?project=244660030535)
2. Find your Web Client ID
3. Ensure the following are properly configured:
    - **Authorized JavaScript origins**: Include your domain (e.g., `http://localhost:8000`, `https://yourdomain.com`)
    - **Authorized redirect URIs**: Include proper redirect URIs (e.g., `http://localhost:8000/auth/google/callback`)

## 6. Restart Your Web Server

After making these changes:

1. Restart your web server (Apache/Nginx)
2. Clear your browser cache or use incognito mode for testing

## 7. Testing

Try signing in with Google again. If you still encounter issues, check the browser console for more specific errors.

## Common Error Solutions

### If you see "Error 400: redirect_uri_mismatch"

-   Check that your redirect URI in the application exactly matches what's configured in Google Cloud Console

### If you see "Error 403: access_denied"

-   Ensure the API is enabled
-   Check that your OAuth credentials are properly configured
-   Verify your application is requesting the correct scopes

### If you still see Cross-Origin errors

-   Try disabling any extensions that might interfere with authentication
-   Ensure your server is properly sending the CORS headers we added
