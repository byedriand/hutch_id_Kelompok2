from docx import Document

doc = Document('srs_hutch.id_Kel2_REAL._1.3.docx')
paragraphs = [para.text for para in doc.paragraphs if para.text.strip()]

# Print semua text untuk analisis detail
print("=== ANALISIS LENGKAP SRS v1.3 ===\n")
print(f"Total paragraphs: {len(paragraphs)}\n")

# Find key sections
for i, para in enumerate(paragraphs):
    # Highlight key sections
    if any(keyword in para for keyword in ['BAB', 'RIWAYAT', 'Tujuan', '4.1', '4.2', '4.3', '4.4', '4.5', '5.', 'Versi', 'Deskripsi dan Prioritas', 'Alur', 'Persyaratan Fungsional']):
        print(f"[{i}] {para}")
        
print("\n\n=== FULL TEXT ===\n")
for i, para in enumerate(paragraphs):
    print(para)
