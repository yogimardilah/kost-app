-- Add fasilitas column to rooms table
ALTER TABLE rooms ADD COLUMN IF NOT EXISTS fasilitas TEXT;

-- Verify the column was added
SELECT column_name, data_type, character_maximum_length
FROM information_schema.columns
WHERE table_name = 'rooms' AND column_name = 'fasilitas';
