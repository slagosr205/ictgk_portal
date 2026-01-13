import { Box, Button, FormLabel, Grid, IconButton, InputAdornment, Link, Paper, TextField } from "@mui/material";
import axios from "axios";
import React, { useState } from "react";
import Swal from "sweetalert2";
import HistoricoUsuarios from "../tablaHistoricaUsuarios/tablaHistoricaporUsuarios";
import PerfilCandidato from "../informacionPersonal/InformacionPersonalCandidato";
import { ImportContacts, TempleBuddhist, UpcomingRounded } from "@mui/icons-material";
import { grey } from "@mui/material/colors";
import ModalCandidato from "../informacionPersonal/StepperNuevoRegistro";


const Busquedas = () => {
  const [dni, setDni] = useState("");
  const [dataInformacionLaboral, setDataInformacionLaboral] = useState([]);
  const [dataCandidatos, setDataCandidatos] = useState(null);
  const [isEmptyContent, setIsEmptyContent] = useState(false);
  const [openModal, setOpenModal] = useState(false);

  const handleOpenModal = () => {
    setOpenModal(true);
  };

  const handleCloseModal = () => {
    setOpenModal(false);
  };


  const handleSearch = async () => {
    // Implement search logic here
    console.log("Searching for DNI:", dni);
    let nuevoDni = ''; // Eliminar caracteres no numéricos
    if (dni.indexOf('-') !== -1) {
      nuevoDni = dni.replace('-', '');
    } else {
      nuevoDni = dni;
    }

    const response = await axios.get(`/infopersonal/${nuevoDni}`)

    if (response.status === 200) {
      console.log("Search successful:", response.data);
      // Handle successful search, e.g., update state or display results
      setDataInformacionLaboral(response.data.laboralInfo);
      setDataCandidatos(response.data.candidatos);
      setIsEmptyContent(false);

      if (response.data.code === 400) {
        console.log("No se encontraron resultados para el DNI ingresado.");
        setIsEmptyContent(true);
      }

    }


  };

  return (
    
    <Grid container >
      
      <Grid item xs={12} md={12} sx={{ padding: 2}} width={"100%"}> 
        {/* Sección 1 */}
        <Grid
          container
          spacing={2}
          alignItems="center"
          border={1}
          borderRadius={3}
          padding={2}
          sx={{ bgcolor: grey[100] }}
        >
          <Grid item xs={12} sm={8} >
            <TextField
              id="dni"
              placeholder="0000000000000"
              fullWidth
              size="small"
              variant="outlined"
              onChange={(e) => setDni(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") {
                  handleSearch();
                }
              }}
            />
          </Grid>
          <Grid item xs={12} sm={4}>
            <Button
              id="btndni"
              variant="outlined"
              color="success"
              fullWidth
              onClick={handleSearch}
            >
              Buscar
            </Button>
          </Grid>
          <Grid item xs={12} sm={6}>
            <Button variant="contained" fullWidth>
              <ImportContacts sx={{ mr: 1 }} />
              Importar Ingresos
            </Button>
          </Grid>
          <Grid item xs={12} sm={6}>
            <Button
              variant="outlined"
              fullWidth
              component={Link}
              href="{{}}"
              color="success"
              startIcon={<UpcomingRounded />}
            >
              Plantilla
            </Button>
          </Grid>
        </Grid>

        {/* Sección 2 */}
        <Grid container spacing={1} padding={2}>
          <Grid item xs={12}>
            <PerfilCandidato dataCandidatos={dataCandidatos} />
          </Grid>
        </Grid>

        {/* Sección 3 */}
        <Grid container spacing={1} padding={2}>
          <Grid item xs={12}>
            <HistoricoUsuarios dataInformacionLaboral={dataInformacionLaboral} />
          </Grid>
        </Grid>
        <Grid container spacing={1} padding={2}>
          {console.log("Data Informacion Laboral:", isEmptyContent)}
          <Grid item xs={12}>
            {isEmptyContent && (
              <Box textAlign="center" color="text.secondary">
                 <Paper elevation={3} sx={{ padding: 2, textAlign: 'center' }}>
                <Grid container spacing={1} padding={2}>
                  <Grid item xs={12}>
                    No se encontraron resultados para el DNI ingresado.
                  </Grid>

                </Grid>
                <Grid container spacing={1} padding={2}>
                  <Grid item xs={12}>
                   
                        <Button sx={{ bgcolor: grey[600], color: 'HighlightText' }} onClick={handleOpenModal} >Crear nuevo Registro</Button>
                        
                          <ModalCandidato open={openModal} onClose={handleCloseModal} candidato={dataCandidatos} dniBuscado={dni} ></ModalCandidato>
                  </Grid>
                </Grid>

                </Paper>
              </Box>
            )}
          </Grid>
        </Grid>
      </Grid>

    </Grid>

  );
}
export default Busquedas;