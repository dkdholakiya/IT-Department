# set_page_colors.py
import re

# ============================================================================
# 1. UPDATE App/Views/auth/login.php
# Default: R-124 G-58 B-237 -> #7C3AED
# ============================================================================
with open('App/Views/auth/login.php', 'r', encoding='utf-8') as f:
    login = f.read().replace('\r\n', '\n')

# A. Insert initial CSS root variables in head so there is zero flash of green
login_head_style = '''  <!-- Default Page Theme Color: Royal Violet (R:124 G:58 B:237) -->
  <style>:root { --color-primary: #7C3AED; --color-primary-rgb: 124, 58, 237; --color-primary-dark: #6D28D9; --color-primary-light: #A78BFA; }</style>
</head>'''
login = login.replace('</head>', login_head_style, 1)

# B. Update colorPickerPopover in login.php
old_login_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#10B981" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#729C51" class="color-preset-btn w-7 h-7 rounded-full bg-[#729C51]" title="Leaf Green (#729C51)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

new_login_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#7C3AED" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED] ring-2 ring-offset-1 ring-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#E58C7F" class="color-preset-btn w-7 h-7 rounded-full bg-[#E58C7F]" title="Warm Coral (#E58C7F)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

login = login.replace(old_login_picker, new_login_picker)

# C. Update JS default in login.php
login = login.replace(
    "primaryHex: localStorage.getItem('portal_auth_color') || '#10B981',",
    "primaryHex: localStorage.getItem('portal_auth_color_login') || '#7C3AED',"
)
login = login.replace(
    "localStorage.setItem('portal_auth_color', state.primaryHex);",
    "localStorage.setItem('portal_auth_color_login', state.primaryHex);"
)

with open('App/Views/auth/login.php', 'w', encoding='utf-8') as f:
    f.write(login)
print("Updated login.php to default #7C3AED (R:124 G:58 B:237)!")


# ============================================================================
# 2. UPDATE App/Views/auth/otp.php
# Default: R-2 G-132 B-199 -> #0284C7
# ============================================================================
with open('App/Views/auth/otp.php', 'r', encoding='utf-8') as f:
    otp = f.read().replace('\r\n', '\n')

# A. Insert initial CSS root variables in head
otp_head_style = '''  <!-- Default Page Theme Color: Ocean Blue (R:2 G:132 B:199) -->
  <style>:root { --color-primary: #0284C7; --color-primary-rgb: 2, 132, 199; --color-primary-dark: #0369A1; --color-primary-light: #38BDF8; }</style>
</head>'''
otp = otp.replace('</head>', otp_head_style, 1)

# B. Update colorPickerPopover in otp.php
old_otp_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#10B981" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#729C51" class="color-preset-btn w-7 h-7 rounded-full bg-[#729C51]" title="Leaf Green (#729C51)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

new_otp_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#0284C7" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7] ring-2 ring-offset-1 ring-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#E58C7F" class="color-preset-btn w-7 h-7 rounded-full bg-[#E58C7F]" title="Warm Coral (#E58C7F)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

otp = otp.replace(old_otp_picker, new_otp_picker)

# C. Update JS default in otp.php
otp = otp.replace(
    "primaryHex: localStorage.getItem('portal_auth_color') || '#10B981',",
    "primaryHex: localStorage.getItem('portal_auth_color_otp') || '#0284C7',"
)
otp = otp.replace(
    "localStorage.setItem('portal_auth_color', hex);",
    "localStorage.setItem('portal_auth_color_otp', hex);"
)

with open('App/Views/auth/otp.php', 'w', encoding='utf-8') as f:
    f.write(otp)
print("Updated otp.php to default #0284C7 (R:2 G:132 B:199)!")


# ============================================================================
# 3. UPDATE App/Views/auth/signup.php
# Default: R-229 G-140 B-127 -> #E58C7F
# ============================================================================
with open('App/Views/auth/signup.php', 'r', encoding='utf-8') as f:
    signup = f.read().replace('\r\n', '\n')

# A. Insert initial CSS root variables in head
signup_head_style = '''  <!-- Default Page Theme Color: Warm Coral (R:229 G:140 B:127) -->
  <style>:root { --color-primary: #E58C7F; --color-primary-rgb: 229, 140, 127; --color-primary-dark: #D47162; --color-primary-light: #F4B2A8; }</style>
</head>'''
signup = signup.replace('</head>', signup_head_style, 1)

# B. Update colorPickerPopover in signup.php
old_signup_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#10B981" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#729C51" class="color-preset-btn w-7 h-7 rounded-full bg-[#729C51]" title="Leaf Green (#729C51)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

new_signup_picker = '''      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#E58C7F" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#E58C7F" class="color-preset-btn w-7 h-7 rounded-full bg-[#E58C7F] ring-2 ring-offset-1 ring-[#E58C7F]" title="Warm Coral (#E58C7F)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>'''

signup = signup.replace(old_signup_picker, new_signup_picker)

# C. Update JS default in signup.php
signup = signup.replace(
    "primaryHex: localStorage.getItem('portal_auth_color') || '#10B981',",
    "primaryHex: localStorage.getItem('portal_auth_color_signup') || '#E58C7F',"
)
signup = signup.replace(
    "localStorage.setItem('portal_auth_color', state.primaryHex);",
    "localStorage.setItem('portal_auth_color_signup', state.primaryHex);"
)

with open('App/Views/auth/signup.php', 'w', encoding='utf-8') as f:
    f.write(signup)
print("Updated signup.php to default #E58C7F (R:229 G:140 B:127)!")

