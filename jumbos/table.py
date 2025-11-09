import pandas as pd

# Load the CSV file
df = pd.read_csv('mlb_2025_pitches.csv')

# Display all columns and top 2 rows
pd.set_option('display.max_columns', None)
print(df.head(2))

# Print all column names
for col in df.columns:
    print(col)

# Show all unique pitch types
print(df['pitcher_name'].dropna().unique())
print("\nUnique pitch types:")
print(df['pitch_type'].dropna().unique())
