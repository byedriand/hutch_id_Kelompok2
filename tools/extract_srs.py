from docx import Document

doc = Document('srs_hutch.id_Kel2_REAL._1.3.docx')
paragraphs = [para.text for para in doc.paragraphs if para.text.strip()]

# Filter yang ada requirements
for para in paragraphs:
    if any(keyword in para for keyword in ['REQ-', '4.1', '4.2', '4.3', '4.4', '4.5', 'Persyaratan Fungsional']):
        print(para)
        print()
