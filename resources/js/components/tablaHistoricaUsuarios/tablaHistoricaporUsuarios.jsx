import React from "react";
import {
  MaterialReactTable,
  createMRTColumnHelper,
  useMaterialReactTable,
} from 'material-react-table';
import { Button } from "@mui/material";
import { CheckBoxOutlineBlank, CheckBoxOutlineBlankOutlined, CheckCircleOutlineRounded, HeartBrokenTwoTone, MonitorHeart } from "@mui/icons-material";
import { green, red } from "@mui/material/colors";


const HistoricoUsuarios = ({ dataInformacionLaboral }) => {
    if (!dataInformacionLaboral || dataInformacionLaboral.length === 0) {
        return null;
        
    }
    
  const columnHelper = createMRTColumnHelper();

  const columns = [
  /*  columnHelper.accessor('id', {
      header: 'ID',
      size: 50,
      
    }),*/
    columnHelper.accessor('nombre', {
      header: 'Nombre',
      size: 300,
    }),
    columnHelper.accessor('area', {
      header: 'area',
      size: 300,
    }),
    columnHelper.accessor('nombrepuesto', {
      header: 'Puesto',
      size: 300,
    }),
    columnHelper.accessor('fechaIngreso', {
      header: 'Fecha de Ingreso',
      size: 300,
    }),

    columnHelper.accessor('Comentario', {
      header: 'Comentarios',
      size: 300,
    }),

    columnHelper.accessor('activo', {
      header: 'Estado',
      size: 300,
      Cell: ({ cell }) => {
        const accion = cell.getValue();
        return (
          <div>
            {accion === 's' ? (
             <MonitorHeart sx={{ color: red[500] }} />
            ) : (
              <CheckCircleOutlineRounded sx={{color:green[500]}}/>
            )}
          </div>
        );
      },
    }),
  ];

  return (
    <MaterialReactTable
      columns={columns}
      data={dataInformacionLaboral}
      enablePagination={true}
      enableSorting={true}
      enableColumnFilter={true}
      
    />
  );
}


export default HistoricoUsuarios;