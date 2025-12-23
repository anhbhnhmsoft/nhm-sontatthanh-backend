# Zalo OAuth Flow Diagram

## Sequence Diagram

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│ Mobile App  │         │   Browser   │         │   Backend   │         │    Zalo     │
└──────┬──────┘         └──────┬──────┘         └──────┬──────┘         └──────┬──────┘
       │                       │                       │                       │
       │ 1. Click "Login"      │                       │                       │
       │──────────────────────>│                       │                       │
       │                       │                       │                       │
       │                       │ 2. GET /auth/zalo/redirect                    │
       │                       │──────────────────────>│                       │
       │                       │                       │                       │
       │                       │                       │ 3. Redirect to Zalo   │
       │                       │                       │──────────────────────>│
       │                       │                       │                       │
       │                       │ 4. Show login page    │                       │
       │                       │<──────────────────────────────────────────────│
       │                       │                       │                       │
       │                       │ 5. User login & approve                       │
       │                       │──────────────────────────────────────────────>│
       │                       │                       │                       │
       │                       │ 6. Redirect with code │                       │
       │                       │<──────────────────────────────────────────────│
       │                       │                       │                       │
       │                       │ 7. GET /auth/zalo/callback?code=xxx           │
       │                       │──────────────────────>│                       │
       │                       │                       │                       │
       │                       │                       │ 8. Exchange code      │
       │                       │                       │──────────────────────>│
       │                       │                       │                       │
       │                       │                       │ 9. Return access_token│
       │                       │                       │<──────────────────────│
       │                       │                       │                       │
       │                       │                       │ 10. Get user profile  │
       │                       │                       │──────────────────────>│
       │                       │                       │                       │
       │                       │                       │ 11. Return profile    │
       │                       │                       │<──────────────────────│
       │                       │                       │                       │
       │                       │                       │ 12. Create/Login user │
       │                       │                       │ (Internal DB)         │
       │                       │                       │                       │
       │                       │ 13. Show callback page with deeplink          │
       │                       │<──────────────────────│                       │
       │                       │                       │                       │
       │ 14. Deeplink: nhmapp://auth/zalo?token=xxx    │                       │
       │<──────────────────────│                       │                       │
       │                       │                       │                       │
       │ 15. Save token & navigate                     │                       │
       │ to home screen        │                       │                       │
       │                       │                       │                       │
       ▼                       ▼                       ▼                       ▼
```

## Component Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                          Mobile Application                         │
│                                                                     │
│  ┌──────────────────┐         ┌──────────────────┐                │
│  │  Login Screen    │         │  Deeplink Handler│                │
│  │                  │         │                  │                │
│  │  [Zalo Button]   │────────>│  Parse URL       │                │
│  │                  │         │  Extract Token   │                │
│  │  Opens Browser   │         │  Save & Navigate │                │
│  └──────────────────┘         └──────────────────┘                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
                              │                    ▲
                              │                    │
                              ▼                    │
                         Open URL            Deeplink Callback
                              │                    │
                              │                    │
┌─────────────────────────────────────────────────────────────────────┐
│                          Web Browser                                │
│                                                                     │
│  ┌──────────────────┐         ┌──────────────────┐                │
│  │  Zalo OAuth Page │         │  Callback Page   │                │
│  │                  │         │                  │                │
│  │  Login Form      │────────>│  Success Message │                │
│  │  Approve Button  │         │  Auto Redirect   │                │
│  │                  │         │  to Deeplink     │                │
│  └──────────────────┘         └──────────────────┘                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
                              │                    ▲
                              │                    │
                              ▼                    │
                         OAuth Flow           Return Token
                              │                    │
                              │                    │
┌─────────────────────────────────────────────────────────────────────┐
│                          Backend Server                             │
│                                                                     │
│  ┌──────────────────┐         ┌──────────────────┐                │
│  │ ZaloAuthController│         │  ZaloService     │                │
│  │                  │         │                  │                │
│  │  redirect()      │────────>│  getAuthUrl()    │                │
│  │  callback()      │         │  exchangeCode()  │                │
│  │                  │         │  getUserProfile()│                │
│  └────────┬─────────┘         └──────────────────┘                │
│           │                                                         │
│           ▼                                                         │
│  ┌──────────────────┐         ┌──────────────────┐                │
│  │  AuthService     │         │  User Model      │                │
│  │                  │         │                  │                │
│  │  authenticate()  │────────>│  Create/Find     │                │
│  │  createToken()   │         │  Update Profile  │                │
│  │                  │         │                  │                │
│  └──────────────────┘         └──────────────────┘                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
                              │                    ▲
                              │                    │
                              ▼                    │
                         API Calls            User Data
                              │                    │
                              │                    │
┌─────────────────────────────────────────────────────────────────────┐
│                          Zalo Platform                              │
│                                                                     │
│  ┌──────────────────┐         ┌──────────────────┐                │
│  │  OAuth Server    │         │  Graph API       │                │
│  │                  │         │                  │                │
│  │  Authorize       │         │  User Profile    │                │
│  │  Token Exchange  │         │  User Info       │                │
│  │                  │         │                  │                │
│  └──────────────────┘         └──────────────────┘                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow

```
┌──────────────┐
│ User clicks  │
│ Zalo button  │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Mobile App opens browser with URL:  │
│ /auth/zalo/redirect                  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend redirects to:                │
│ https://oauth.zaloapp.com/v4/...     │
│ ?app_id=xxx&redirect_uri=...         │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ User logs in and approves on Zalo   │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Zalo redirects back with code:      │
│ /auth/zalo/callback?code=ABC123      │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend exchanges code for token:    │
│ POST oauth.zaloapp.com/v4/access_token│
│ Body: {code, app_id, app_secret}     │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Zalo returns: {access_token: "..."}  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend gets user profile:           │
│ GET graph.zalo.me/v2.0/me            │
│ Header: {access_token: "..."}        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Zalo returns:                        │
│ {id, name, picture}                  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend creates/finds user in DB:    │
│ - Check if zalo_id exists            │
│ - Create new user if not             │
│ - Generate JWT token                 │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend shows callback page with:    │
│ nhmapp://auth/zalo?token=JWT&...     │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Browser triggers deeplink            │
│ Mobile app receives and parses       │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Mobile app:                          │
│ - Extracts token from URL            │
│ - Saves to AsyncStorage              │
│ - Navigates to home screen           │
└──────────────────────────────────────┘
```

## State Diagram

```
┌─────────────┐
│   Initial   │
│  (Logged    │
│    Out)     │
└──────┬──────┘
       │
       │ User clicks Zalo button
       │
       ▼
┌─────────────┐
│  Opening    │
│  Browser    │
└──────┬──────┘
       │
       │ Browser opened
       │
       ▼
┌─────────────┐
│   Zalo      │
│   Login     │
│   Page      │
└──────┬──────┘
       │
       │ User approves
       │
       ▼
┌─────────────┐
│ Processing  │
│ Callback    │
└──────┬──────┘
       │
       │ Token generated
       │
       ▼
┌─────────────┐
│ Redirecting │
│ to App      │
└──────┬──────┘
       │
       │ Deeplink triggered
       │
       ▼
┌─────────────┐
│   Logged    │
│     In      │
└─────────────┘
```

## Error Handling Flow

```
┌─────────────┐
│   Start     │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────┐
│ User clicks Zalo button     │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Can open browser?           │────>│ Show error   │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Zalo OAuth page loads?      │────>│ Network error│
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ User approves?              │────>│ User denied  │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Code exchange successful?   │────>│ Token error  │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Get user profile successful?│────>│ API error    │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Create/login user successful│────>│ DB error     │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────────────────────┐     ┌──────────────┐
│ Deeplink works?             │────>│ Config error │
└──────┬──────────────────────┘ No  └──────────────┘
       │ Yes
       ▼
┌─────────────┐
│   Success   │
└─────────────┘
```
