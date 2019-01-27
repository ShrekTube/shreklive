# API notes

### Functions list

NOTE: Replace {api_key} with a valid key from any user account

#### Stream functions

  - /api/{api_key}/stream/info - returns all active stream information
  - /api/{api_key}/stream/ping - returns recording status, stream URL, and watch URL for all current live channels
  - /api/{api_key}/stream/ping/<displayname> - returns stream live status, recording status, stream URL, and watch URL for a specific channel
  - /api/{api_key}/stream/record-start/<displayname> - starts recording the specified channel
  - /api/{api_key}/stream/record-stop/<displayname> - stops recording the specified channel

#### Subscription functions

  - /api/{api_key}/subscription/add/<displayname> - Add current user (verified through API key) as a subscriber to specified channel
  - /api/{api_key}/subscription/remove/<displayname> - Remove current user (verified through API key) as a subscriber to specified channel
  - /api/{api_key}/subscription/list - Show list of all current subscriptions for your account (subscribers and subscribed). Account shown is tied to API key

#### Chat Functions

- /api/{api_key}/chat/read/<displayname> - Reads the last 60 lines associated with a channel's chat room
- /api/{api_key}/chat/write - Writes a chatline with POST data to the database using the following: channel, timestamp, user, message, type
  - Channel: Matches current display_name of a valid channel
  - Timestamp: Unix epoch
  - User: Current user by display_name
  - Type: Valid types are SYSTEM or USER. SYSTEM message types are formatted differently on output.
- /api/{api_key}/chat/join/<displayname> - Sends a join message to the specified channel as the user. User is tied to the API key.
- /api/{api_key}/chat/leave/<displayname> - Sends a part message to the specified channel as the user. User is tied to the API key.
